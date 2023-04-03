<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnpublishedWebsitePageRequest extends AbstractRequest
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
            'website_page_category_uuid' => ['required', 'numeric', Rule::exists('website_page_categories','uuid')->whereNull('deleted_at')],
            'is_default' => ['required', 'boolean'],
            'display_type' => ['required', 'string', 'in:page,in_page']
        ];
    }
}
