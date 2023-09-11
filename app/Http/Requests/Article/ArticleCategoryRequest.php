<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class ArticleCategoryRequest extends AbstractRequest
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
            'feature_image' => ['nullable', 'string'],
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_categories')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'keyword' => ['required', 'array', 'min:1'],
            'keyword.en' => ['required', 'string'],
            'keyword.*' => ['required', 'string'],
            'description' => ['nullable', 'array', 'min:1'],
            'description.*' => ['nullable', 'string'],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2'],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
