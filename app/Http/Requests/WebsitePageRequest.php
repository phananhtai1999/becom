<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use Illuminate\Validation\Rule;

class WebsitePageRequest extends AbstractRequest
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
        $website = new WebsitePage();
        return [
            'title' => ['required', 'string'],
            'slug' => ['nullable', 'string'],
            'html_template' => ['required', 'string'],
            'css_template' => ['required', 'string'],
            'js_template' => ['required', 'string'],
            'template_json' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(
                $website->getTypeWebsitePage()
            )],
            'website_page_category_uuid' => ['required', 'numeric', Rule::exists('website_page_categories', 'uuid')->whereNull('deleted_at')],
            'is_default' => ['required', 'boolean'],
            'display_type' => ['required', 'string', 'in:page,in_page'],
            'feature_image' => ['nullable', 'string'],
            'keyword' => ['nullable', 'array'],
            'keyword.en' => ['required_with:keyword', 'string', 'not_in:0'],
            'keyword.*' => ['required_with:keyword', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['required_with:description', 'string', 'not_in:0'],
            'description.*' => ['required_with:description', 'string'],
        ];
    }
}
