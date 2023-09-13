<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class LanguageResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $excludeColumns = $request->get('exclude', []);

        $data = [
            'code' => $this->getKey(),
            'name' => $this->name,
            'flag_image' => $this->flag_image,
            'status' => $this->status,
            'fe' => $this->fe,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        //Remove unnecessary value
        foreach ($excludeColumns as $column) {
            if (in_array($column, array_keys($data))) {
                $data[$column] = null;
            }
        }

        return $data;
    }
}
