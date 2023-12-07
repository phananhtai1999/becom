<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\BusinessCategory;
use App\Models\Purpose;
use Illuminate\Validation\Rule;

class MyMailTemplateRequest extends AbstractRequest
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
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'send_project_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {

                return $query->where([
                    ['user_uuid', auth()->user()],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            })],
            'business_category_uuid' => ['required', 'numeric', 'min:1', Rule::exists('business_categories', 'uuid')->where(function ($q) {
                return $q->where('publish_status', BusinessCategory::PUBLISHED_PUBLISH_STATUS)->whereNull('deleted_at');
            })],
            'purpose_uuid' => ['required', 'numeric', 'min:1', Rule::exists('purposes', 'uuid')->where(function ($q) {
                return $q->where('publish_status', Purpose::PUBLISHED_PUBLISH_STATUS)->whereNull('deleted_at');
            })],
            'design' => ['required', 'string'],
            'type' => ['required', 'string', 'in:sms,email,telegram,viber'],
            'image' => ['nullable', 'array'],
            'image.*' => ['string'],
        ];
    }
}
