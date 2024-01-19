<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\ArticleSeriesRule;
use Illuminate\Validation\Rule;

class ArticleSeriesRequest extends AbstractRequest
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
        $validate = [
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_series')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'list_keywords' => ['nullable', 'in:NULL'],
            'article_category_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_series', 'uuid')->whereNull('deleted_at')],
//            'assigned_ids' => ['nullable', 'numeric', new ArticleSeriesRule($this->request->get('assigned_ids'))],
            'assigned_ids' => ['nullable', 'string'],
        ];

        if ($this->request->get('parent_uuid')) {
            $validate['article_category_uuid'] = ['nullable', 'in:NULL'];
            $validate['assigned_ids'] = ['nullable', 'in:NULL'];
            $validate['list_keywords'] = ['required', 'array', 'min:1'];
            $validate['list_keywords.*'] = ['string'];
        }

        return $validate;
    }
}
