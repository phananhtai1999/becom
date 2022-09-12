<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\MyCampaignQueryBuilder;
use Carbon\Carbon;

class MyCampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = MyCampaignQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyCampaignByKeyOrAbort($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyCampaignByKey($id)
    {
        $campaign = $this->findMyCampaignByKeyOrAbort($id);

        return $this->destroy($campaign->getKey());
    }

    /**
     * @return mixed
     */
    public function CheckMyCampaign($campaignUuid)
    {
        $myCampaign = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['websites.user_uuid', auth()->user()->getKey()],
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
            ])->first();

        if($myCampaign){
            return true;
        }
        return false;
    }

    /**
     * @param $model
     * @return array
     */
    public function findContactKeyByMyCampaign($model)
    {
        $contacts = $model->contacts()->get();

        if (empty($contacts)) {

            return [];
        } else {
            foreach ($contacts as $contact) {
                $contactUuid[] = $contact->uuid;

                return $contactUuid;
            }
        }
    }
}
