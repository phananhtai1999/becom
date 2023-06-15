<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateUnpublishedArticleRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => ['nullable', 'string'],
            'video' => ['nullable', 'string'],
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('articles')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.en' => ['string'],
            'title.*' => ['string'],
            'content' => ['array', 'min:1'],
            'content.en' => ['string'],
            'content.*' => ['string'],
            'content_for_user' => ['nullable', 'string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
