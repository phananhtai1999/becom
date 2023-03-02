<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ArticleResource extends AbstractJsonResource
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
            'article_category_uuid' => $this->article_category_uuid,
            'user_uuid' => $this->user_uuid,
            'image' => $this->image,
            'publish_status' => $this->publish_status,
            'title' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->title : $this->getTranslations('title'),
            'content' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->title : $this->getTranslations('title'),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('article__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('article__article_category', $expand)) {
            $data['article_category'] = new ArticleCategoryResource($this->articleCategory);
        }

        return $data;
    }
}