<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyArticleCategoryRequest extends AbstractRequest
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
        //        if (empty(auth()->user()->team)) {
//            $validates['publish_status'] = array_merge(['required'], $validates['publish_status']);
//        }
        return [
            'image' => ['nullable', 'string'],
            'feature_image' => ['nullable', 'string'],
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_categories')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'keyword' => ['nullable', 'array'],
            'keyword.en' => ['required_with:keyword', 'string', 'not_in:0'],
            'keyword.*' => ['required_with:keyword', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['required_with:description', 'string', 'not_in:0'],
            'description.*' => ['required_with:description', 'string'],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2'],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
