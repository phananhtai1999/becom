<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\UploadImgRequest;
use App\Http\Requests\UploadVideoRequest;
use App\Services\UploadService;
use App\Services\UserService;
use Aws\Exception\CredentialsException;
use Aws\S3\Exception\S3Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UploadController extends AbstractRestAPIController
{
    /**
     * @var UploadService
     */
    protected $uploadService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UploadService $uploadService
     * @param UserService $userService
     */
    public function __construct(
        UploadService $uploadService,
        UserService   $userService
    )
    {
        $this->uploadService = $uploadService;
        $this->userService = $userService;
    }

    /**
     * @param UploadImgRequest $request
     * @return JsonResponse
     */
    public function uploadImage(UploadImgRequest $request)
    {
        return $this->upload($request->img, $request->type);
    }

    /**
     * @param UploadVideoRequest $request
     * @return JsonResponse
     */
    public function uploadVideo(UploadVideoRequest $request)
    {
        return $this->upload($request->video, $request->type);
    }

    /**
     * @param $uploadType
     * @param $type
     * @return JsonResponse
     */
    public function upload($uploadType, $type)
    {
        try {
            //File structure by role
            $char = $this->userService->getCurrentUserRole();
            $imageName = $char . '-' . Str::uuid() . '_' . time() . '.' . $uploadType->getClientOriginalExtension();
            //Bucket S3
            $configS3 = $this->uploadService->getStorageServiceByType($type);
            //Check storage service exists or not
            $disk = $this->uploadService->storageBuild($configS3);
            //Upload
            $path = $disk->putFileAs('public/upload', $uploadType, $imageName);

            return $this->sendCreatedJsonResponse([
                'data' => [
                    "slug" => $path,
                    "absolute_slug" => $disk->url($path)
                ]
            ]);
        } catch (S3Exception|\InvalidArgumentException|CredentialsException $exception) {
            return $this->sendValidationFailedJsonResponse();
        }
    }
}
