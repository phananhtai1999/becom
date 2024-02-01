<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Techup\ApiBase\Services\AppCallService;

class CstoreService extends AppCallService
{
    public function storeS3Config($request)
    {
        try{
            $data = [
                "access_key" => $request->get('access_key'),
                "secret_access_key" => $request->get('secret_access_key'),
                "default_region" => $request->get('default_region'),
                "bucket" => $request->get('bucket'),
                "name" => $request->get('name')
            ];

            $this->callService(env('CSTORE_SERVICE_NAME'), 'post', 'sem-s3-config', $data, auth()->appId(), auth()->userId());

            return true;
        }catch (\Exception  $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function storeFolderByType($name, $uuid, $type, $parentUuid = null, $parentType = null)
    {
        try{
            $data = [
                "name" => $name,
                "owner_uuid" => $uuid,
                "owner_type" => $type,
                "parent_owner_uuid" => $parentUuid,
                "parent_owner_type" => $parentType,
            ];
            $this->callService(env('CSTORE_SERVICE_NAME'), 'post', 'sem-folder-type', $data, auth()->appId(), auth()->userId());
            return true;
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function deleteFolderType($uuid, $type, $option)
    {
        try{
            $data = [
                "owner_uuid" => $uuid,
                "owner_type" => $type,
                "option" => $option
            ];

            $this->callService(env('CSTORE_SERVICE_NAME'), 'post', 'sem-delete-folder-type', $data, auth()->appId(), auth()->userId());
            return true;
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }
}
