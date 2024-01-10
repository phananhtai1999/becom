<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MapShortcodeRequest extends AbstractRequest
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
            'short_code_group_uuid'=> ['required', 'integer', 'exists:short_code_groups,uuid'],
            'short_code_uuids'=> ['required', 'array'],
            'short_code_uuids.*' => ['required', 'integer', 'min:1', 'distinct','exists:website_page_short_codes,uuid']
        ];
    }
}
