<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class CstoreService extends AbstractService
{
    public function storeS3Config($request)
    {
        try{
            $client = $this->createRequest();
            $client->post(config('shop.cstore_url') . 's3-config', [
                'json' => [
                    "access_key" => $request->get('access_key'),
                    "secret_access_key" => $request->get('secret_access_key'),
                    "default_region" => $request->get('default_region'),
                    "bucket" => $request->get('bucket'),
                    "name" => $request->get('name')
                ]
            ]);

            return true;
        }catch (\Exception  $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function storeFolderByType($name, $uuid, $type, $parentUuid = null, $parentType = null)
    {
        try{
            $client = $this->createRequest();
            $client->post(config('shop.cstore_url') . 'folder-type', [
                'json' => [
                    "name" => $name,
                    "owner_uuid" => $uuid,
                    "owner_type" => $type,
                    "parent_owner_uuid" => $parentUuid,
                    "parent_owner_type" => $parentType,
                ]
            ]);

            return true;
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }
}
