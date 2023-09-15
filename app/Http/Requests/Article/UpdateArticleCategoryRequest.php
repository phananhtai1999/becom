<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use App\Rules\CustomDescriptionRule;
use App\Rules\CustomKeywordRule;
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
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_categories')->ignore($this->id, 'uuid')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->where(function ($query) {
                return $query->where('uuid', "<>", $this->id)->whereNull('deleted_at');
            })],
            'keyword' => ['nullable', 'array', new CustomKeywordRule($this->id, $this->request->get('keyword'), 'article_categories')],
            'keyword.*' => ['nullable', 'string', 'not_in:0'],
            'description' => ['nullable', 'array', new CustomDescriptionRule($this->id, $this->request->get('keyword'), $this->request->get('description'), 'article_categories')],
            'description.*' => ['nullable', 'string', 'not_in:0'],
        ];
    }
}
