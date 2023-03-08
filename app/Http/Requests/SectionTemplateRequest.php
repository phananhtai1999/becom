<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class SectionTemplateRequest extends AbstractRequest
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
            'title' => ['required', 'string'],
            'template' => ['required', 'string'],
            'template_json' => ['required', 'string'],
            'section_category_uuid' => ['required', 'numeric', Rule::exists('section_categories','uuid')->whereNull('deleted_at')],
            'is_default' => ['required', 'boolean']
        ];
    }
}
