<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Language;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends AbstractRequest
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
            'code' => ['string', Rule::in(app(Language::class)->languagesSupport), Rule::unique('languages','code')->ignore($this->id, 'code')],
            'name' => ['string', Rule::unique('languages','name')->ignore($this->id, 'code')],
            'flag_image' => ['nullable', 'string'],
            'fe' => ['json'],
            'status' => ['boolean']
        ];
    }
}
