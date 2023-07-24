<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\ArticleService;

class ArticleResource extends AbstractJsonResource
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

        $formatContent = (new ArticleService())->formatContent($this->content_type, $this->content, $this->content_translate);

        $data = [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'article_category_uuid' => $this->article_category_uuid,
            'user_uuid' => $this->user_uuid,
            'content_type' => $this->content_type,
            'single_purpose_uuid' => $this->single_purpose_uuid,
            'paragraph_type_uuid' => $this->paragraph_type_uuid,
            'image' => $this->image,
            'video' => $this->video,
            'publish_status' => $this->publish_status,
            'reject_reason' => $this->reject_reason,
            'content_for_user' => $this->content_for_user,
            'title' => $this->title,
            'titles' => $this->titles,
            'content' => $formatContent['content'],
            'content_translate' => $formatContent['content_translate'],
            'short_content' => $this->short_content,
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

        if (\in_array('article__single_purpose', $expand)) {
            $data['single_purpose'] = new SinglePurposeResource($this->singlePurpose);
        }

        if (\in_array('article__paragraph_type', $expand)) {
            $data['paragraph_type'] = new ParagraphTypeResource($this->paragraphType);
        }

        return $data;
    }
}
