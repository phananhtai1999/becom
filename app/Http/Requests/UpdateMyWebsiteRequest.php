<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SectionTemplate;
use App\Models\Website;
use App\Rules\CheckIsCanUseSectionTemplate;
use App\Rules\CheckUniqueSlugWebsitePageRule;
use App\Rules\CheckWebsiteDomainRule;
use App\Rules\CheckWebsitePagesRule;
use Illuminate\Validation\Rule;

class UpdateMyWebsiteRequest extends AbstractRequest
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
                        ['user_uuid', auth()->user()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->where('uuid', '<>', $this->request->get('footer_section_uuid'))
                    ->whereNull('deleted_at');
            }), CheckIsCanUseSectionTemplate::IsCanUseSectionTemplate($this->request->get('header_section_uuid'), $this->id)],
            'footer_section_uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                    $q->where([
                        ['user_uuid', auth()->user()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->where('uuid', '<>', $this->request->get('header_section_uuid'))
                    ->whereNull('deleted_at');
            }), CheckIsCanUseSectionTemplate::IsCanUseSectionTemplate($this->request->get('footer_section_uuid'), $this->id)],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'domain_uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('domains', 'uuid')->where(function ($q) {
                return $q->where('owner_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            }), CheckWebsiteDomainRule::uniqueDomain($this->id)],
            'website_pages' => ['nullable', 'array', CheckWebsitePagesRule::singleHomepage(), CheckWebsitePagesRule::uniqueWebpageIds()],
            'website_pages.*.uuid' => ['nullable', 'required_unless:publish_status,' . Website::DRAFT_PUBLISH_STATUS, 'numeric', Rule::exists('website_pages', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                    $q->where([
                        ['user_uuid', auth()->user()],
                        ['app_id', auth()->appId()]
                    ])
                        ->orWhere('is_default', true);
                })->whereNull('deleted_at');
            }), new CheckUniqueSlugWebsitePageRule($this->request->get('website_pages'))],
            'website_pages.*.is_homepage' => ['nullable', 'boolean'],
            'website_pages.*.ordering' => ['nullable', 'numeric', 'min:1'],
            'tracking_ids' => ['nullable', 'array'],
            'tracking_ids.*' => ['nullable', 'string', 'max:300'],
            'publish_status' => ['numeric', Rule::in(Website::BLOCKED_PUBLISH_STATUS, Website::DRAFT_PUBLISH_STATUS)],
        ];
    }
}
