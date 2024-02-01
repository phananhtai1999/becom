<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use App\Rules\CustomDescriptionRule;
use App\Rules\CustomKeywordRule;
use Illuminate\Validation\Rule;

class UpdateMyWebsitePageRequest extends AbstractRequest
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
            'menu_level' => ['integer'],
            'type' => ['string', Rule::in(
                (new WebsitePage())->getTypeWebsitePage()
            )],
            'template_json' => ['string'],
            'website_page_category_uuid' => ['numeric', Rule::exists('website_page_categories', 'uuid')->whereNull('deleted_at')],
            'publish_status' => ['numeric', 'min:1', 'max:2'],
            'display_type' => ['string', 'in:page,in_page'],
            'feature_image' => ['nullable', 'string'],
            'keyword' => ['nullable', 'array', new CustomKeywordRule($this->id, $this->request->get('keyword'), 'website_page')],
            'keyword.*' => ['nullable', 'string', 'not_in:0'],
            'description' => ['nullable', 'array', new CustomDescriptionRule($this->id, $this->request->get('keyword'), $this->request->get('description'), 'website_page')],
            'description.*' => ['nullable', 'string', 'not_in:0'],
        ];

        if ($this->request->get('type') == WebsitePage::NEWS_HEADER_TYPE || $this->request->get('type') == WebsitePage::PRODUCT_HEADER_TYPE) {
            $validate['menu_level'] = array_merge($validate['menu_level'], ['required']);
        }

        return $validate;
    }
}
