<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessCategoryRequest extends AbstractRequest
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
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'publish_status' => ['numeric', 'min:1', 'max:2'],
            'parent_uuid' => ['nullable', 'numeric', Rule::exists('business_categories', 'uuid')->where(function ($query) {
                return $query->where('uuid',"<>", $this->id)->whereNull('deleted_at');
            })],
        ];
    }
}
