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
use App\Services\AssetService;

class AssetController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestIndexTrait;

    public function __construct(AssetService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = AssetResourceCollection::class;
        $this->resourceClass = AssetResource::class;
        $this->storeRequest = AssetRequest::class;
        $this->editRequest = UpdateAssetRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    public function store(AssetRequest $request)
    {
        $model = $this->service->create($request->all());

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
//        $jsCode = '<script>
//    function ShowBanners() {
//        const image = document.createElement("img");
//        image.setAttribute("src", "' . $asset->url . '?ref=' . $partner->code . '");
//        image.setAttribute("height", "' . $asset->assetSize->height . '");
//        image.setAttribute("width", "' . $asset->assetSize->width . '");
//        const link = document.createElement("a");
//        link.href = "' . $request->get('url') . '";
//        link.appendChild(image);
//        document.getElementById("banner-ads").appendChild(link);
//    }
//</script>';
//        $asset->js_code = $jsCode;
        $jsCode = '<script type="text/javascript" src="http://localhost:8000/generate.php?pn='. $partner->uuid .'&as='. $id .'&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        return $this->sendOkJsonResponse(['data' => $jsCode]);
    }
}
