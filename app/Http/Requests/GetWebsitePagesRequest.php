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
            'website_uuid' => ['nullable', 'integer', Rule::exists('websites', 'uuid')],
            'domain' => [(Rule::requiredIf(empty($this->request->get('website_uuid')))), 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'website_page_slug' => ['nullable', 'string', Rule::exists('website_pages', 'slug')
                ->whereNull('deleted_at')],
            'article_slug' => ['exists:articles,slug'],
            'article_category_slug' => ['exists:article_categories,slug', function ($attribute, $value, $fail) {
                $articleSlug = $this->request->get('article_slug');

                if ($articleSlug && $articleSlug === $value) {
                    $fail("Article slug can't same with article category slug.");
                }
            }],
            'article_uuid' => ['exists:articles,uuid'],
            'article_category_uuid' => ['exists:article_categories,uuid', function ($attribute, $value, $fail) {
                $articleUuid = $this->request->get('article_uuid');

                if ($articleUuid && $articleUuid === $value) {
                    $fail("Article slug can't same with article category slug.");
                }
            }]
        ];
    }
}
