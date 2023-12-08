<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\ChangeStatusAssetRequest;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\GenerateJsCodeAssetRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UnpublishedAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Requests\UpdateUnpublishedAssetRequest;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetResourceCollection;
use App\Models\Asset;
use App\Models\Role;
use App\Services\AssetService;
use App\Services\UploadService;
use App\Services\UserService;
use Carbon\Carbon;

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
        $model = $this->service->create(array_merge(
            $request->except('status'),
            [
                'url' => $uploadUrl['absolute_slug'],
                'status' => Asset::PUBLISH_STATUS,
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]));

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
            $this->service->update($model, array_merge($request->except('status'), ['url' => $uploadUrl['absolute_slug']]));
        } else {
            $this->service->update($model, $request->all());
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function indexMy(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            [
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId()
            ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function destroyMy($id)
    {
        $asset = $this->service->findOrFailById($id);
        if ($asset->status == Asset::PUBLISH_STATUS) {

            return $this->sendJsonResponse(false, __('asset.deleteMy.failed_publish'), [], 400);
        }
        $this->service->destroyMyAsset($id);

        return $this->sendJsonResponse(true, __('asset.delete.success'), [], 200);
    }

    public function generateJsCode(GenerateJsCodeAssetRequest $request)
    {
        if (empty(auth()->user()->partner) && auth()->user()->role != Role::ROLE_ADMIN) {
            return $this->sendJsonResponse(false, __('asset.failed_partner'), [], 403);
        }
        $mainUrl = $this->service->getConfigByKeyInCache('main_url');
        if (!preg_match('/^' . preg_quote($mainUrl->value, '/') . '/', $request->get('url'))) {
            return $this->sendJsonResponse(false, __('asset.failed_main_url', ['main_url' => $mainUrl->value]), [], 400);
        }

        $partner = auth()->user()->partner;
        $asset = $this->service->findOrFailById($request->get('asset_uuid'));
        if ($asset->type == Asset::TYPE_IMAGE) {
            $jsCode = '<script type="text/javascript" src="' . asset("/") . 'api/generate-image?pn=' . $partner->uuid . '&as=' . $asset->uuid . '&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        } else {
            $jsCode = '<script type="text/javascript" src="' . asset("/") . 'api/generate-video?pn=' . $partner->uuid . '&as=' . $asset->uuid . '&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
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

    public function changeStatusAsset($id, ChangeStatusAssetRequest $request)
    {
        $model = $this->service->findOrFailById($id);
        $list_reason = $model->reject_reason;
        if ($request->get('status') == Asset::REJECT_STATUS) {
            $list_reason[] = [
                'content' => $request->get('reject_reason'),
                'created_at' => Carbon::now()
            ];
        }
        $model->update([
            'status' => $request->get('status'),
            'reject_reason' => $list_reason
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storePendingAsset(UnpublishedAssetRequest $request)
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
        $model = $this->service->create(array_merge(
            $request->all(),
            [
                'url' => $uploadUrl['absolute_slug'],
                'user_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]));

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
        if (empty(auth()->user()->partner) && !$this->service->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            return $this->sendJsonResponse(false, __('asset.failed_partner'), [], 403);
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
        $model = $this->service->showAssetForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editPendingAsset(UpdateUnpublishedAssetRequest $request, $id)
    {
        $model = $this->service->showAssetForEditorById($id);
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
            $this->service->update($model, array_merge($request->all(), [
                'url' => $uploadUrl['absolute_slug']
            ]));
        } else {
            $this->service->update($model, $request->all());
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function editorAssetChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $data = $this->service->editorAssetChart($groupBy, $startDate, $endDate);
        $total = $this->service->totalEditorAssetChart($startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => $total
        ]);
    }

}
