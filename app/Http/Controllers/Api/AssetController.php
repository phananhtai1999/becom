<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\GenerateJsCodeAssetRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetResourceCollection;
use App\Models\Asset;
use App\Models\Role;
use App\Services\AssetService;
use App\Services\UploadService;
use App\Services\UserService;

class AssetController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestIndexTrait;

    public function __construct(AssetService $service, UserService $userService, UploadService $uploadService)
    {
        $this->service = $service;
        $this->userService = $userService;
        $this->uploadService = $uploadService;
        $this->resourceCollectionClass = AssetResourceCollection::class;
        $this->resourceClass = AssetResource::class;
        $this->storeRequest = AssetRequest::class;
        $this->editRequest = UpdateAssetRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function store(AssetRequest $request)
    {
        $uploadUrl = $this->uploadFile($request->file, $this->userService->getCurrentUserRole(), $this->uploadService);
        $filename = $uploadUrl['absolute_slug'];
        if ($request->get('type') == 'image') {
            if (getimagesize($filename)['mime'] == 'image/gif') {
                $gif = $this->service->validateGif($filename, $uploadUrl);
                if ($gif['is_failed']) {
                    $this->deleteFile($uploadUrl['slug'], $this->uploadService);
                    return $this->sendJsonResponse(false, $gif['message'], [], 400);
                }
            }
        }
        $model = $this->service->create(array_merge($request->except('status'), ['url' => $uploadUrl['absolute_slug'], 'status' => Asset::PUBLISH_STATUS]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function edit(UpdateAssetRequest $request, $id)
    {
        $model = $this->service->findOrFailById($id);
        if ($request->file) {
            $uploadUrl = $this->uploadFile($request->file, $this->userService->getCurrentUserRole(), $this->uploadService);
            $filename = $uploadUrl['absolute_slug'];
            if ($request->get('type') == 'image') {
                if (getimagesize($filename)['mime'] == 'image/gif') {
                    $gif = $this->service->validateGif($filename, $uploadUrl);
                    if ($gif['is_failed']) {
                        $this->deleteFile($uploadUrl['slug'], $this->uploadService);
                        return $this->sendJsonResponse(false, $gif['message'], [], 400);
                    }
                }
            }
            $this->service->update($model, array_merge($request->all(), ['url' => $uploadUrl['absolute_slug']]));
        } else {
            $this->service->update($model, $request->all());
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function generateJsCode(GenerateJsCodeAssetRequest $request)
    {
        if (empty(auth()->user()->partner) && auth()->user()->role != Role::ADMIN_ROOT) {
            return $this->sendJsonResponse(false, 'You need become partner to use it', [], 403);
        }
        $mainUrl = $this->service->getConfigByKeyInCache('main_url');
        if (!preg_match('/^' . preg_quote($mainUrl->value, '/') . '/', $request->get('url'))) {
            return $this->sendJsonResponse(false, 'Your url must be start with ' . $mainUrl->value, [], 400);
        }

        $partner = auth()->user()->partner;
        $asset = $this->service->findOrFailById($request->get('asset_uuid'));
        if ($asset->type == Asset::TYPE_IMAGE) {
            $jsCode = '<script type="text/javascript" src="' . env('FRONTEND_URL') . 'api/generate-image?pn=' . $partner->uuid . '&as=' . $asset->uuid . '&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        } else {
            $jsCode = '<script type="text/javascript" src="' . env('FRONTEND_URL') . 'api/generate-video?pn=' . $partner->uuid . '&as=' . $asset->uuid . '&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        }

        return $this->sendOkJsonResponse(['data' => $jsCode]);
    }

    public function generateForImage(\Illuminate\Http\Request $request)
    {
        $asset = $this->service->findOrFailById($request->get('as'));
        echo 'const image = document.createElement("img");
        image.setAttribute("src", "' . $asset->url . '");
        image.setAttribute("height", "' . $asset->assetSize->height . '");
        image.setAttribute("width", "' . $asset->assetSize->width . '");
        const link = document.createElement("a");
        link.href = "' . $request->get('link') . '";
        link.appendChild(image);
        document.getElementById("banner-ads").appendChild(link);';
    }

    public function generateForVideo(\Illuminate\Http\Request $request)
    {
        $asset = $this->service->findOrFailById($request->get('as'));
        echo 'const video = document.createElement("video");
        video.src = "' . $asset->url . '";
        video.autoplay = true;
        video.controls = true;
        video.muted = false;
        video.height = ' . $asset->assetSize->height . ';
        video.width = ' . $asset->assetSize->width . ';

        const box = document.getElementById("banner-ads");
        const link = document.createElement("a");
        link.href = "' . $request->get('link') . '";
        link.appendChild(video);
        box.appendChild(link);';
    }

    public function publishAsset($id)
    {
        $model = $this->service->findOrFailById($id);
        $model->update(['status' => Asset::PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storePendingAsset(AssetRequest $request)
    {
        $uploadUrl = $this->uploadFile($request->file, $this->userService->getCurrentUserRole(), $this->uploadService);
        $filename = $uploadUrl['absolute_slug'];
        if ($request->get('type') == 'image') {
            if (getimagesize($filename)['mime'] == 'image/gif') {
                $gif = $this->service->validateGif($filename, $uploadUrl);
                if ($gif['is_failed']) {
                    $this->deleteFile($uploadUrl['slug'], $this->uploadService);
                    return $this->sendJsonResponse(false, $gif['message'], [], 400);
                }
            }
        }
        $model = $this->service->create(array_merge($request->except('status'), ['url' => $uploadUrl['absolute_slug']]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function pendingAssets(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['status' => Asset::PENDING_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function indexPublishAssets(IndexRequest $request)
    {
        if (empty(auth()->user()->partner) && !auth()->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ADMIN_ROOT])) {
            return $this->sendJsonResponse(false, 'You need to become partner to use it', [], 403);
        }
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['status' => Asset::PUBLISH_STATUS]);
        $this->service->addJsCodeToIndex($models);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPendingAsset($id)
    {
        $model = $this->service->findOneWhereOrFail(['status' => Asset::PENDING_STATUS, 'uuid' => $id]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editPendingAsset(UpdateAssetRequest $request, $id)
    {
        $model = $this->service->findOneWhereOrFail(['status' => Asset::PENDING_STATUS, 'uuid' => $id]);
        if ($request->file) {
            $uploadUrl = $this->uploadFile($request->file, $this->userService->getCurrentUserRole(), $this->uploadService);
            $filename = $uploadUrl['absolute_slug'];
            if ($request->get('type') == 'image') {
                if (getimagesize($filename)['mime'] == 'image/gif') {
                    $gif = $this->service->validateGif($filename, $uploadUrl);
                    if ($gif['is_failed']) {
                        $this->deleteFile($uploadUrl['slug'], $this->uploadService);
                        return $this->sendJsonResponse(false, $gif['message'], [], 400);
                    }
                }
            }
            $this->service->update($model, array_merge($request->except('status'), ['url' => $uploadUrl['absolute_slug']]));
        } else {
            $this->service->update($model, $request->all());
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

}
