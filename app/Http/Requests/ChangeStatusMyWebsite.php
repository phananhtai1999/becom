<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Website;
use Illuminate\Validation\Rule;

class ChangeStatusMyWebsite extends AbstractRequest
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
            'websites' => ['required', 'array', 'min:1'],
            'websites.*' => [
                'numeric',
                'min:1',
                Rule::exists('websites', 'uuid')->where(function ($query) {
                    return $query->where([
                        ['user_uuid', auth()->userId()],
                        ['app_id', auth()->appId()],
                        ['publish_status', '<>', $this->request->get('publish_status')]
                    ]);
                }),
                function ($attribute, $value, $fail) {
                    $website = Website::find($value);
                    $isVerified = ($website && $website->domain && $website->domain->verified_at);
                    if (!$isVerified && $this->request->get('publish_status') == Website::PUBLISHED_PUBLISH_STATUS) {
                        $fail(__('messages.domain_must_active'));
                    }
                }
            ],
            'publish_status' => ['required', 'numeric', Rule::in(Website::PUBLISHED_PUBLISH_STATUS, Website::BLOCKED_PUBLISH_STATUS)]
        ];
    }
}
