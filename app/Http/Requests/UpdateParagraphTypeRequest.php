<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateParagraphTypeRequest extends AbstractRequest
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
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('paragraph_types')->ignore($this->id,'uuid')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'parent_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('paragraph_types', 'uuid')->where(function ($query) {
                return $query->where('uuid',"<>", $this->id)->whereNull('deleted_at');
            })],
            'sort' => ['numeric']
        ];
    }
}
