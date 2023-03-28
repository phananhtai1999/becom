<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateActivityHistoryRequest extends AbstractRequest
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
            'type' => ['string'],
            'type_id' => ['nullable', 'numeric', Rule::exists('mail_sending_history','uuid')->whereNull('deleted_at')],
            'contact_uuid' => ['numeric', 'min:1', Rule::exists('contacts','uuid')->whereNull('deleted_at')],
            'date' => ['date'],
            'content' => ['array', 'min:1'],
            'content.langkey' => ['required', 'string'],
            'content.*' => ['string']
        ];
    }
}
