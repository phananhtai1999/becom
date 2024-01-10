<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Article;
use App\Models\SectionTemplate;
use App\Models\Website;
use App\Rules\CheckIsCanUseSectionTemplate;
use App\Rules\CheckUniqueSlugWebsitePageRule;
use App\Rules\CheckWebsiteDomainRule;
use App\Rules\CheckWebsitePagesRule;
use App\Rules\UniqueWebsitePage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsiteRequest extends AbstractRequest
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
            'title' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'string'],
            'header_section_uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                    $q->where([
                        ['user_uuid', auth()->userId()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->where('uuid', '<>', $this->request->get('footer_section_uuid'))
                    ->whereNull('deleted_at');
            }), CheckIsCanUseSectionTemplate::IsCanUseSectionTemplate($this->request->get('header_section_uuid'))],
            'footer_section_uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                    $q->where([
                        ['user_uuid', auth()->userId()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->where('uuid', '<>', $this->request->get('header_section_uuid'))
                    ->whereNull('deleted_at');
            }), CheckIsCanUseSectionTemplate::IsCanUseSectionTemplate($this->request->get('footer_section_uuid'))],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'domain_uuid' => ['nullable', 'numeric', Rule::exists('domains', 'uuid')->where(function ($q) {
                return $q->where([
                    ['owner_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->whereNull('deleted_at');
            }), CheckWebsiteDomainRule::uniqueDomain($this->id)],
            'website_pages' => ['nullable', 'array', 'distinct', CheckWebsitePagesRule::singleHomepage(), CheckWebsitePagesRule::uniqueWebpageIds()],
            'website_pages.*.uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('website_pages', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                    $q->where([
                        ['user_uuid', auth()->userId()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->whereNull('deleted_at');
            }), new CheckUniqueSlugWebsitePageRule($this->request->get('website_pages')), new UniqueWebsitePage($this->request->get('website_pages'))],
            'website_pages.*.is_homepage' => ['nullable', 'boolean'],
            'website_pages.*.ordering' => ['nullable', 'numeric', 'min:1'],
            'tracking_ids' => ['nullable', 'array'],
            'tracking_ids.*' => ['nullable', 'string', 'max:300'],
            'user_uuid' => ['nullable', 'string', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'publish_status' => ['required', 'numeric', Rule::in(Website::PUBLISHED_PUBLISH_STATUS, Website::DRAFT_PUBLISH_STATUS)],
            'is_active_news_page' => ['boolean'],
            'is_active_product_page' => ['boolean'],
        ];
    }
}
