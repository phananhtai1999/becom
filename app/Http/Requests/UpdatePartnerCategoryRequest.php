<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdatePartnerCategoryRequest extends AbstractRequest
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
            'image' => ['nullable', 'string'],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'content' => ['array', 'min:1'],
            'content.*' => ['string'],
        ];
    }
}
