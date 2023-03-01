<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use Illuminate\Validation\Rule;

class AcceptPublishWebsitePageRequest extends AbstractRequest
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
            'website_pages' => ['required', 'array', 'min:1'],
            'website_pages.*' => ['numeric', 'min:1', Rule::exists('website_pages', 'uuid')->where(function ($query) {
                return $query->where('publish_status', WebsitePage::PENDING_PUBLISH_STATUS)->whereNull('deleted_at');
            })],
        ];
    }
}
