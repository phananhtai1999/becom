<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Services\LanguageService;
use Illuminate\Validation\Rule;

class LanguageRequest extends AbstractRequest
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
            'code' => ['required', 'string', Rule::in(app(LanguageService::class)->languagesSupport()), Rule::unique('languages','code')],
            'name' => ['required', 'string', Rule::unique('languages','name')],
            'flag_image' => ['nullable', 'string'],
            'fe' => ['required', 'json'],
            'status' => ['required', 'boolean']
        ];
    }
}
