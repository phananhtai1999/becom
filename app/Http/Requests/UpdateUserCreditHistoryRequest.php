<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateUserCreditHistoryRequest extends AbstractRequest
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
            'user_uuid' => ['numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'add_by_uuid' => ['numeric', 'min:1'],
            'credit' => ['numeric', 'min:1'],
        ];
    }
}
