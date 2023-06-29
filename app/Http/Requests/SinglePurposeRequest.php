<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class SinglePurposeRequest extends AbstractRequest
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
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('single_purposes')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'parent_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('single_purposes', 'uuid')->whereNull('deleted_at')],
        ];
    }
}
