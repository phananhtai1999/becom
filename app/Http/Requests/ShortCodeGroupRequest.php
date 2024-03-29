<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShortCodeGroupRequest extends AbstractRequest
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
            'key' => ['required', 'string', Rule::unique('short_code_groups', 'key')->whereNull('deleted_at')],
            'name' => ['required', 'string'],
            'description' => ['string']
        ];
    }
}
