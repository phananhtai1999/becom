<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class StatusMyScenarioRequest extends AbstractRequest
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
            'scenario_uuid' => ['required', 'numeric', 'min:1', Rule::exists('scenarios', 'uuid')->where(function ($query){
                return $query->where('user_uuid', auth()->user()->getKey())->whereNull('deleted_at');
            })],
            'status' => ['required', 'in:running,stopped', Rule::unique('scenarios', 'status')->where(function ($query){
                return $query->where('uuid', $this->request->get('scenario_uuid'))
                    ->where('user_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })]
        ];
    }
}
