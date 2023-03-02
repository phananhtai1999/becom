<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ArticleCategoryResource extends AbstractJsonResource
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
            'slug' => $this->slug,
            'title' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->title : $this->getTranslations('title'),
            'image' => $this->image,
            'parent_uuid' => $this->parent_uuid,
            'user_uuid' => $this->user_uuid,
            'publish_status' => $this->publish_status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('article_category__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('article_category__parent_category', $expand)) {
            $data['parent_category'] = new ArticleCategoryResource($this->parentArticleCategory);
        }

        if (\in_array('article_category__children_category', $expand)) {
            $data['children_category'] = self::collection($this->childrenArticleCategory);
        }

        return $data;
    }
}
