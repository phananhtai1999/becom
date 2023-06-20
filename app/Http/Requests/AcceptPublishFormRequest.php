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
        $validate =  [
            'forms' => ['required', 'array', 'min:1'],
            'forms.*' => ['numeric', 'min:1', Rule::exists('forms', 'uuid')->where(function ($query) {

                return $query->where('publish_status', '<>', $this->request->get('publish_status'))->whereNull('deleted_at');
            })],
            'publish_status' => ['required', 'numeric', Rule::in(Form::PUBLISHED_PUBLISH_STATUS, Form::REJECT_PUBLISH_STATUS)]
        ];

        if ($this->request->get('publish_status') == Form::REJECT_PUBLISH_STATUS){
            $validate['reject_reason'] = ['required', 'string'];
        }

        return $validate;
    }
}
