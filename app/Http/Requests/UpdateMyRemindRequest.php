<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyRemindRequest extends AbstractRequest
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
            'name' => ['string'],
            'note' => ['string'],
            'date' => ['date'],
            'contact' => ['array', 'min:1'],
            'contact.*' => ['numeric', 'min:1', Rule::exists('contacts', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
        ];
    }
}
