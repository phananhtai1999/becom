<?php

namespace App\Http\Resources;

use App\Http\Requests\AssetGroupRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'title' => $this->title,
            'url' => $this->url,
            'user_uuid' => $this->user_uuid,
            'asset_size_uuid' => $this->asset_size_uuid,
            'status' => $this->status,
            'reject_reason' => $this->reject_reason,
            'js_code' => $this->js_code
        ];

        if (\in_array('asset__asset_size', $expand)) {
            $data['asset_size'] = new AssetSizeResource($this->assetSize);
        }
        if (\in_array('asset__asset_group', $expand)) {
            $data['asset_group'] = new AssetGroupResource(optional($this->assetSize)->assetGroup);
        }
        if (\in_array('asset__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
