<?php

namespace App\Http\Requests;

use App\Models\WebsitePage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetWebsitePagesRequest extends FormRequest
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
            'domain' => ['required', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'website_page_slug' => ['nullable', 'string', Rule::exists('website_pages', 'slug')
                ->whereNull('deleted_at')],
            'article_slug' => ['exists:articles,slug'],
            'article_category_slug' => ['exists:article_categories,slug', function ($attribute, $value, $fail) {
                $articleCategorySlug = $this->request->get('article_slug');

                if ($articleCategorySlug && $articleCategorySlug === $value) {
                    $fail("Article slug can't same with article category slug.");
                }
            }]
        ];
    }
}
