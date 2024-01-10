<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetInfoByDomainUrlRequest extends AbstractRequest
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
            'domain' => ['required', 'exists:domains,name'],
            'website_page_slug' => ['exists:website_pages,slug'],
            'article_slug' => ['exists:articles,slug'],
            'article_category_slug' => ['exists:article_categories,slug']
        ];
    }
}
