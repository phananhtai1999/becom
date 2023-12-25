<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
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
            'name' => ['string', Rule::unique('locations', 'name')
                ->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                ->whereNull('deleted_at')],
            'address' => ['string']
        ];
    }
}
