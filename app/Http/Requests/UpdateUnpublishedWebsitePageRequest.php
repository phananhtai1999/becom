<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use App\Rules\CustomDescriptionRule;
use App\Rules\CustomKeywordRule;
use Illuminate\Validation\Rule;

class UpdateUnpublishedWebsitePageRequest extends AbstractRequest
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
            'title' => ['string'],
            'slug' => ['string'],
            'template' => ['string'],
            'template_json' => ['string'],
            'website_page_category_uuid' => ['numeric', Rule::exists('website_page_categories', 'uuid')->whereNull('deleted_at')],
            'is_default' => ['boolean'],
            'display_type' => ['string', 'in:page,in_page'],
            'publish_status' => ['numeric', Rule::in(WebsitePage::PENDING_PUBLISH_STATUS, WebsitePage::DRAFT_PUBLISH_STATUS)],
            'feature_image' => ['nullable', 'string'],
            'keyword' => ['nullable', 'array', new CustomKeywordRule($this->id, $this->request->get('keyword'), 'website_page')],
            'keyword.*' => ['nullable', 'string', 'not_in:0'],
            'description' => ['nullable', 'array', new CustomDescriptionRule($this->id, $this->request->get('keyword'), $this->request->get('description'), 'website_page')],
            'description.*' => ['nullable', 'string', 'not_in:0'],
        ];
    }
}
