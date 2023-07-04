<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\ParagraphTypeService;
use App\Services\UserService;

class ParagraphTypeResource extends AbstractJsonResource
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
            'title_translate' => $this->title_translate,
            'parent_uuid' => $this->parent_uuid,
            'user_uuid' => $this->user_uuid,
            'sort' => $this->sort,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('paragraph_type__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('paragraph_type__parent_category', $expand)) {
            $data['parent_category'] = new ParagraphTypeResource($this->parentParagraphType);
        }

        if (\in_array('paragraph_type__children_category', $expand)) {
            $sortChildren = (new ParagraphTypeService())->sortChildren();
            $childrenParagraphType = $sortChildren ? $this->sortDescChildrenParagraphType : $this->childrenParagraphType;
            $data['children_category'] = self::collection($childrenParagraphType);
        }

        if (\in_array('paragraph_type__articles', $expand)) {
            $data['articles'] = ArticleResource::collection($this->articles);
        }

        return $data;
    }
}
