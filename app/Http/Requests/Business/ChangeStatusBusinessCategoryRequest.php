<?php

namespace App\Http\Requests\Business;

use App\Abstracts\AbstractRequest;
use App\Models\BusinessCategory;
use Illuminate\Validation\Rule;

class ChangeStatusBusinessCategoryRequest extends AbstractRequest
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
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2', Rule::unique('business_categories', 'publish_status')->where(function ($q) {
                return $q->where('publish_status', $this->request->get('publish_status'))
                    ->where('uuid', $this->id)
                    ->whereNull('deleted_at');
            })],
            'business_category_uuid' => ['nullable',"required_if:publish_status,".BusinessCategory::PENDING_PUBLISH_STATUS,Rule::exists('business_categories', 'uuid')->where(function ($q) {
                return $q->where('publish_status', BusinessCategory::PUBLISHED_PUBLISH_STATUS)
                    ->where('uuid', '<>', $this->id)->whereNull('deleted_at');
            })]
        ];
    }
}
