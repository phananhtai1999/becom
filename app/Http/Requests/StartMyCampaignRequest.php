<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class StartMyCampaignRequest extends AbstractRequest
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
            'campaign_uuid' => ['required', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query){
                return $query->where('user_uuid', auth()->user()->getKey())->whereNull('deleted_at');
            })],
            'was_stopped_by_owner' => ['required', 'boolean', Rule::unique('campaigns', 'was_stopped_by_owner')->where(function ($query){
                return $query->where('uuid', $this->request->get('campaign_uuid'))
                    ->where('user_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
        ];
    }
}
