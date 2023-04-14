<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyMailTemplateRequest extends AbstractRequest
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
            'subject' => ['string'],
            'body' => ['string'],
            'send_project_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'design' => 'string',
            'type' => ['string', 'in:sms,email,telegram,viber'],
            'image' => ['nullable', 'array'],
            'image.*' => ['string'],
        ];
    }
}
