<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    /**
     * @param $perPage
     * @return mixed
     */
    public function indexMyCampaign($perPage)
    {
        return $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findMyCampaignByKeyOrAbort($id)
    {
        $smtpAccount = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('campaigns.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $smtpAccount;
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function deleteMyCampaignByKey($id)
    {
        $smtpAccount = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('campaigns.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $this->destroy($smtpAccount->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }
}
