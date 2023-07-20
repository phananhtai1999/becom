<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class ArticleSeriesResource extends AbstractJsonResource
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
            'article_category_uuid' => $this->article_category_uuid,
            'parent_uuid' => $this->parent_uuid,
            'list_keywords' => $this->list_keywords,
            'title' => app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title,
            'title_translate' => $this->title_translate,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('article_series__article_category', $expand)) {
            $data['article_category'] = new ArticleCategoryResource($this->articleCategory);
        }

        if (\in_array('article_series__parent_category', $expand)) {
            $data['parent_category'] = new ArticleSeriesResource($this->parentArticleSeries);
        }

        if (\in_array('article_series__children_category', $expand)) {
            $data['children_category'] = self::collection($this->childrenArticleSeries);
        }

        return $data;
    }
}