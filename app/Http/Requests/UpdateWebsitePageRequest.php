<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use App\Rules\CustomDescriptionRule;
use App\Rules\CustomKeywordRule;
use Illuminate\Validation\Rule;

class UpdateWebsitePageRequest extends AbstractRequest
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
            'title' => ['string'],
            'slug' => ['string'],
            'html_template' => ['string'],
            'css_template' => ['string'],
            'js_template' => ['string'],
            'template_json' => ['string'],
            'website_page_category_uuid' => ['numeric', Rule::exists('website_page_categories','uuid')->whereNull('deleted_at')],
            'type' => ['string', Rule::in(WebsitePage::STATIC_TYPE,WebsitePage::ARTICLE_DETAIL_TYPE, WebsitePage::ARTICLE_CATEGORY_TYPE, WebsitePage::HOME_ARTICLES_TYPE)],
            'publish_status' => ['numeric', 'min:1', 'max:4'],
            'is_default' => ['boolean'],
            'display_type' => ['string', 'in:page,in_page'],
            'feature_image' => ['nullable', 'string'],
            'keyword' => ['nullable', 'array', new CustomKeywordRule($this->id, $this->request->get('keyword'), 'website_page')],
            'keyword.*' => ['nullable', 'string', 'not_in:0'],
            'description' => ['nullable', 'array', new CustomDescriptionRule($this->id, $this->request->get('keyword'), $this->request->get('description'), 'website_page')],
            'description.*' => ['nullable', 'string', 'not_in:0'],
        ];

        return $validate;
    }
}
