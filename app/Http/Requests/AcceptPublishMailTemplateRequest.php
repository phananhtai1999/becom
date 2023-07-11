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
        $validate = [
            'mail_templates' => ['required', 'array', 'min:1'],
            'mail_templates.*' => ['numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) {

                return $query->where([
                    ['publish_status', '<>', $this->request->get('publish_status')],
                    ['publish_status', '<>', MailTemplate::DRAFT_PUBLISH_STATUS]
                ])->whereNull('deleted_at');
            })],
            'publish_status' => ['required', 'numeric', Rule::in(MailTemplate::PUBLISHED_PUBLISH_STATUS, MailTemplate::REJECT_PUBLISH_STATUS, MailTemplate::PENDING_PUBLISH_STATUS, MailTemplate::DRAFT_PUBLISH_STATUS)]
        ];

        if ($this->request->get('publish_status') == MailTemplate::REJECT_PUBLISH_STATUS){
            $validate['reject_reason'] = ['required', 'string'];
        }

        return $validate;
    }
}
