<?php

namespace App\Http\Requests\Business;

use App\Abstracts\AbstractRequest;
use App\Models\BusinessCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroyBusinessCategoryRequest extends AbstractRequest
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
            'business_category_uuid' => ['required', Rule::exists('business_categories', 'uuid')->where(function ($q) {
                return $q->where('publish_status', BusinessCategory::PUBLISHED_PUBLISH_STATUS)
                    ->where('uuid', '<>', $this->id)->whereNull('deleted_at');
            })]
        ];
    }
}
