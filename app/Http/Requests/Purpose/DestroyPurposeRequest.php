<?php

namespace App\Http\Requests\Purpose;

use App\Abstracts\AbstractRequest;
use App\Models\Purpose;
use Illuminate\Validation\Rule;

class DestroyPurposeRequest extends AbstractRequest
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
            'purpose_uuid' => ['required',Rule::exists('purposes', 'uuid')->where(function ($q) {
                return $q->where('publish_status', Purpose::PUBLISHED_PUBLISH_STATUS)
                    ->where('uuid', '<>', $this->id)->whereNull('deleted_at');
            })]
        ];
    }
}
