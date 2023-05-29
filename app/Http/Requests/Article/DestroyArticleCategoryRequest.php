<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use App\Models\ArticleCategory;
use App\Models\BusinessCategory;
use Illuminate\Validation\Rule;

class DestroyArticleCategoryRequest extends AbstractRequest
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
            'article_category_uuid' => ['nullable', Rule::exists('article_categories', 'uuid')->where(function ($q) {
                return $q->where('publish_status', ArticleCategory::PUBLISHED_PUBLISH_STATUS)
                    ->where('uuid', '<>', $this->id)->whereNull('deleted_at');
            })]
        ];
    }
}
