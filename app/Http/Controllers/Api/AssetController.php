<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\GenerateJsCodeAssetRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetResourceCollection;
use App\Services\AssetService;
use App\Services\UploadService;
use App\Services\UserService;
use Exception;
use Imagick;

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
        $filename = $uploadUrl['absolute_slug'];
        $duration = $this->getGifDuration($filename);
        $loop = $this->getGifLoopCount($filename);
        if (empty($loop) || $duration > 30 || $loop * $duration > 30) {
            $this->deleteFile($uploadUrl['slug'], $this->uploadService);

            return $this->sendJsonResponse(false, 'The gif longer than 30s', [], 400);
        } elseif ($this->getFrames($filename) / $duration > 5) {
            $this->deleteFile($uploadUrl['slug'], $this->uploadService);

            return $this->sendJsonResponse(false, 'The gif must be smaller than 5FPS', [], 400);
        }
        $model = $this->service->create(array_merge($request->all(), ['url' => $uploadUrl['absolute_slug']]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function getGifDuration($filename)
    {
        $gifData = file_get_contents($filename);

        $delayPositions = [];
        $offset = 0;
        while (($position = strpos($gifData, "\x21\xF9\x04", $offset)) !== false) {
            $delayPositions[] = $position + 4;
            $offset = $position + 1;
        }

        $totalDuration = 0;
        foreach ($delayPositions as $position) {
            $delayBytes = substr($gifData, $position, 2);
            $delayTime = unpack('v', $delayBytes)[1];
            $totalDuration += $delayTime;
        }

        return $totalDurationSec = $totalDuration / 100.0;
    }

    public function getFrames($filename)
    {
        $gifData = file_get_contents($filename);
        $lastFramePosition = strrpos($gifData, "\x00\x2C");

        return substr_count($gifData, "\x00\x21\xF9\x04", 0, $lastFramePosition + 1);
    }

    public function generateJsCode(GenerateJsCodeAssetRequest $request, $id)
    {
        if (empty(auth()->user()->partner)) {
            return $this->sendJsonResponse(false, 'You need become partner to use it', [], 400);
        }
        $partner = auth()->user()->partner;
        $jsCode = '<script type="text/javascript" src="' . env('FRONTEND_URL') . 'api/generate?pn=' . $partner->uuid . '&as=' . $id . '&link=' . $request->get('url') . '?ref=' . $partner->code . '"> </script>';
        return $this->sendOkJsonResponse(['data' => $jsCode]);
    }

    function getGifLoopCount($filepath)
    {
        $gifData = file_get_contents($filepath);

// Locate the loop count block

// Find the loop count using regular expressions
        preg_match('/\x21\xFF\x0B(?:\x4E\x45\x54\x53\x43\x41\x50\x45\x32\x2E\x30\x03\x01(.{2}))/', $gifData, $matches);
        $loopCount = isset($matches[1]) ? unpack('v', $matches[1])[1] : 0;

        return $loopCount;
    }


    public function generate(\Illuminate\Http\Request $request)
    {
        $asset = $this->service->findOrFailById($request->get('as'));
        echo 'function ShowBanners() {
        const image = document.createElement("img");
        image.setAttribute("src", "' . $asset->url . '");
        image.setAttribute("height", "' . $asset->height . '");
        image.setAttribute("width", "' . $asset->width . '");
        const link = document.createElement("a");
        link.href = "' . $request->get('link') . '";
        link.appendChild(image);
        document.getElementById("banner-ads").appendChild(link);
    }';
    }
}
