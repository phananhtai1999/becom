<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class SinglePurposeResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'title' => app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title,
            'titles' => $this->titles,
            'parent_uuid' => $this->parent_uuid,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('single_purpose__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('single_purpose__parent_category', $expand)) {
            $data['parent_category'] = new SinglePurposeResource($this->parentSinglePurpose);
        }

        if (\in_array('single_purpose__children_category', $expand)) {
            $data['children_category'] = self::collection($this->childrenSinglePurpose);
        }

        if (\in_array('single_purpose__articles', $expand)) {
            $data['articles'] = ArticleResource::collection($this->articles);
        }

        return $data;
    }
}
