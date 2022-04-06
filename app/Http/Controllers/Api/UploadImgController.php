<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\UploadImgRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadImgController extends AbstractRestAPIController
{
    /**
     * @param UploadImgRequest $request
     * @return JsonResponse
     */
    public function upload(UploadImgRequest $request): JsonResponse
    {
        $image = $request->img;
        $imageName = Str::uuid() . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/upload', $imageName, 's3');

        return $this->sendCreatedJsonResponse([
            'data' => [
                "slug" => $path,
                "absolute_slug" => Storage::disk('s3')->url($path)
            ]
        ]);
    }
}
