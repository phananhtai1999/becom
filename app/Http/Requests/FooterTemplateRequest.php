<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class FooterTemplateRequest extends AbstractRequest
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
            'template' => ['required', 'string'],
            'template_json' => ['required', 'string'],
            'type' => ['required', 'string', 'in:sms,email,telegram,viber'],
            'template_type' => ['required', 'in:ads,subscribe']
        ];
    }
}
