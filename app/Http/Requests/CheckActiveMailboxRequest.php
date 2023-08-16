<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\CheckActiveMailBoxRule;
use Illuminate\Validation\Rule;

class CheckActiveMailboxRequest extends AbstractRequest
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
            'domain_uuid' => ['required', 'numeric', 'min:1', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where([
                    ['owner_uuid', auth()->user()->getKey()],
                    ['verified_at', '<>', null],
                ]);
            })->whereNull('deleted_at'), new CheckActiveMailBoxRule($this->request->get('domain_uuid'))]
        ];
    }
}
