<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Campaign;
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
        $campaignUuid = [];
        if (isset(auth()->user()->userTeamContactLists) && !empty(auth()->user()->userTeamContactLists)  && !auth()->user()->userTeam['is_blocked']) {
            $campaignUuid = app(Campaign::class)->select('campaigns.*')
                ->join('campaign_contact_list', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
                ->WhereIn('campaign_contact_list.contact_list_uuid', auth()->user()->userTeamContactLists()->pluck('contact_list_uuid'))->get()->pluck('uuid');
        }
        return [
            'campaign_uuid' => ['required', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query) use ($campaignUuid) {
                return $query->where('user_uuid', auth()->user()->getKey())->orwhereIn('uuid', $campaignUuid);
            })->whereNull('deleted_at')],
            'was_stopped_by_owner' => ['required', 'boolean', Rule::unique('campaigns', 'was_stopped_by_owner')->where(function ($query){
                return $query->where('uuid', $this->request->get('campaign_uuid'))
                    ->where('user_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
        ];
    }
}
