<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends AbstractRequest
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
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('articles')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'content' => ['required', 'array', 'min:1'],
            'content.en' => ['required', 'string'],
            'content.*' => ['required', 'string'],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:4'],
            'content_for_user' => ['nullable', 'string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
