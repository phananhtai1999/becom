<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class DomainRequest extends AbstractRequest
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
            'name' => ['required', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'verified_at' => ['nullable', 'date'],
            'business_uuid' => ['nullable', 'numeric', Rule::exists('business_managements', 'uuid')->where(function ($query) {
                return $query->where([
                    ['owner_uuid', $this->request->get('owner_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })
                ->whereNull('deleted_at')],
            'owner_uuid' => ['nullable', 'string', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
        ];
    }
}
