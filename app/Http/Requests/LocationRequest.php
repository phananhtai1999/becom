<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('locations', 'name')->where(function ($q) {
                return $q->where([
                    ['user_uuid', auth()->user()],
                    ['app_id', auth()->appId()]
                ]);
            })
                ->whereNull('deleted_at')],
            'address' => ['string'],
        ];
    }
}
