<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Config;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class UploadService extends AbstractService
{
    /**
     * @param $type
     * @return mixed
     */
    public function getStorageServiceByType($type)
    {
        if (in_array($type, \config('filestructure.website'))) {
            $configS3 = (new ConfigService())->findConfigByKey(Config::CONFIG_S3_WEBSITE);
        } elseif (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN, Role::ROLE_EDITOR])) {
            $configS3 = (new ConfigService())->findConfigByKey(Config::CONFIG_S3_SYSTEM);
        } else {
            $configS3 = (new ConfigService())->findConfigByKey(Config::CONFIG_S3_USER);
        }

        return $configS3;
    }

    /**
     * @param $configS3
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function storageBuild($configS3)
    {
        if ($configS3) {
            return Storage::build([
                'driver' => $configS3->value['driver'],
                'key' => $configS3->value['key'],
                'secret' => $configS3->value['secret'],
                'region' => $configS3->value['region'],
                'bucket' => $configS3->value['bucket'],
                'url' => array_key_exists('url', $configS3->value) ? $configS3->value['url'] : null,
                'endpoint' => $configS3->value['endpoint'],
                'use_path_style_endpoint' => array_key_exists('use_path_style_endpoint', $configS3->value) && $configS3->value['use_path_style_endpoint'] ? boolval($configS3->value['use_path_style_endpoint']) : true,
            ]);
        } else {
            return Storage::build([
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            ]);
        }
    }
}
