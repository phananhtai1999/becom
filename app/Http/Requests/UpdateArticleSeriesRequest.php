<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateArticleSeriesRequest extends AbstractRequest
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
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('article_series')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'article_category_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('article_series', 'uuid')->whereNull('deleted_at')]
        ];

        if ($this->request->get('parent_uuid')) {
            $validate['list_keywords'] = ['required', 'array', 'min:1'];
            $validate['list_keywords.*'] = ['string'];
        } else {
            $validate['list_keywords'] = ['nullable', 'in:NULL'];
        }

        return $validate;
    }
}
