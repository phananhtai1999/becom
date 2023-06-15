<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Form;
use App\Models\MailTemplate;
use Illuminate\Validation\Rule;

class AcceptPublishFormRequest extends AbstractRequest
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
            'forms' => ['required', 'array', 'min:1'],
            'forms.*' => ['numeric', 'min:1', Rule::exists('forms', 'uuid')->where(function ($query) {

                return $query->where('publish_status', Form::PENDING_PUBLISH_STATUS)->whereNull('deleted_at');
            })],
            'publish_status' => ['required', 'numeric', Rule::in(Form::PUBLISHED_PUBLISH_STATUS, Form::REJECT_PUBLISH_STATUS)]
        ];
    }
}
