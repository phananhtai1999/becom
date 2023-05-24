<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\GenerateJsCodeAssetRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetResourceCollection;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetService;
use App\Services\UploadService;
use App\Services\UserService;
use http\Client\Request;

class AssetController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait;

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
        $model = $this->service->create(array_merge($request->all(), ['url' => $uploadUrl['absolute_slug']]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function generateJsCode(GenerateJsCodeAssetRequest $request, $id)
    {
        if(empty(auth()->user()->partner)) {
            return $this->sendJsonResponse(false, 'You need become partner to use it', [], 400);
        }
        $partner = auth()->user()->partner;
        $jsCode = '<script type="text/javascript" src="' . env('FRONTEND_URL') . 'api/generate?pn='. $partner->uuid .'&as='. $id .'&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        return $this->sendOkJsonResponse(['data' => $jsCode]);
    }

    public function generate(\Illuminate\Http\Request $request) {
        $asset = $this->service->findOrFailById($request->get('as'));
        echo 'function ShowBanners() {
        const image = document.createElement("img");
        image.setAttribute("src", "https://www.simplilearn.com/ice9/free_resources_article_thumb/what_is_image_Processing.jpg");
        image.setAttribute("height", "'. $asset->height .'");
        image.setAttribute("width", "'. $asset->width .'");
        const link = document.createElement("a");
        link.href = "' . $request->get('link') .'";
        link.appendChild(image);
        document.getElementById("banner-ads").appendChild(link);
    }';
    }
}
