<?php

namespace App\Http\Requests\Purpose;

use App\Abstracts\AbstractRequest;
use App\Models\Purpose;
use Illuminate\Validation\Rule;

class ChangeStatusPurposeRequest extends AbstractRequest
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
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2', Rule::unique('purposes', 'publish_status')->where(function ($q) {
                return $q->where('publish_status', $this->request->get('publish_status'))
                    ->where('uuid', $this->id)
                    ->whereNull('deleted_at');
            })],
            'purpose_uuid' => ['nullable',Rule::exists('purposes', 'uuid')->where(function ($q) {
                return $q->where('publish_status', Purpose::PUBLISHED_PUBLISH_STATUS)
                    ->where('uuid', '<>', $this->id)->whereNull('deleted_at');
            })]
        ];
    }
}
