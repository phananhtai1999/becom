<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\MailTemplate;
use Illuminate\Validation\Rule;

class AcceptPublishMailTemplateRequest extends AbstractRequest
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
            'mail_templates' => ['required', 'array', 'min:1'],
            'mail_templates.*' => ['numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) {

                return $query->where('publish_status', MailTemplate::PENDING_PUBLISH_STATUS)->whereNull('deleted_at');
            })],
        ];
    }
}
