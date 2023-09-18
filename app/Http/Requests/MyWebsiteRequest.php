<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SectionTemplate;
use App\Rules\CheckWebsitePagesRule;
use Illuminate\Validation\Rule;

class MyWebsiteRequest extends AbstractRequest
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
            'title' => ['required', 'string'],
            'header_section_uuid' => ['required', 'numeric', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                        $q->where('user_uuid', auth()->user()->getKey())
                            ->orWhere('is_default', true);
                    })->where('publish_status', SectionTemplate::PUBLISHED_PUBLISH_STATUS)
                    ->whereNull('deleted_at');
            })],
            'footer_section_uuid' => ['required', 'numeric', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                        $q->where('user_uuid', auth()->user()->getKey())
                            ->orWhere('is_default', true);
                    })->where('publish_status', SectionTemplate::PUBLISHED_PUBLISH_STATUS)
                    ->whereNull('deleted_at');
            })],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'domain_uuid' => ['required', 'numeric', Rule::exists('domains', 'uuid')->where(function ($q) {
                return $q->where('owner_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
            'website_pages' => ['nullable','array', 'distinct', CheckWebsitePagesRule::singleHomepage(), CheckWebsitePagesRule::uniqueWebpageIds()],
            'website_pages.*.uuid' =>  ['required','numeric', Rule::exists('website_pages', 'uuid')->where(function ($query) {
                return $query->where(function ($q) {
                        $q->where('user_uuid', auth()->user()->getKey())
                            ->orWhere('is_default', true);
                    })->whereNull('deleted_at');
            })],
            'website_pages.*.is_homepage' =>  ['nullable','boolean'],
            'website_pages.*.ordering' =>  ['nullable','numeric', 'min:1'],
            'tracking_ids' => ['nullable', 'array'],
            'tracking_ids.*' => ['nullable', 'string', 'max:300'],
        ];
    }
}
