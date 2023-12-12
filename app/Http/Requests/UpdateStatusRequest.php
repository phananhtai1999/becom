<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends AbstractRequest
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
            'name' => ['array', 'min:1'],
            'name.*' => ['string'],
            'points' => ['numeric', 'min:0', Rule::unique('status', 'points')
                ->where('user_uuid', $this->get('user_uuid', null))
                ->ignore($this->id, 'uuid')
                ->whereNull('deleted_at')],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('user_profiles', 'uuid')->whereNull('deleted_at')],
        ];
    }
}
