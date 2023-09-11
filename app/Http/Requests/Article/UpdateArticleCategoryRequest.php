<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateArticleCategoryRequest extends AbstractRequest
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
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_categories')->ignore($this->id,'uuid')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'keyword' => ['array', 'min:1'],
            'keyword.*' => ['string'],
            'description' => ['nullable', 'array', 'min:1'],
            'description.*' => ['nullable', 'string'],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->where(function ($query) {
                return $query->where('uuid',"<>", $this->id)->whereNull('deleted_at');
            })],
        ];
    }
}
