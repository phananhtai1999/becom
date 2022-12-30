<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MailSendingHistoryResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'campaign_uuid' => $this->campaign_uuid,
            'campaign_scenario_uuid' => $this->campaign_scenario_uuid,
            'email' => $this->email,
            'time' => $this->time,
            'status' => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('mail_sending_history__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        if (\in_array('mail_sending_history__campaign_scenario', $expand)) {
            $data['campaign_scenario'] = new CampaignScenarioResource($this->campaignScenario);
        }

        if (\in_array('mail_sending_history__website', $expand)) {
            $data['website'] = new WebsiteResource($this->campaign->website);
        }

        if (\in_array('mail_sending_history__mail_template', $expand)) {
            $data['mail_template'] = new MailTemplateResource($this->campaign->mailTemplate);
        }

        if (\in_array('mail_sending_history__smtp_account', $expand)) {
            $data['smtp_account'] = new SmtpAccountResource($this->campaign->smtpAccount);
        }

        if (\in_array('mail_sending_history__user', $expand)) {
            $data['user'] = new UserResource($this->campaign->user);
        }

        if (\in_array('mail_sending_history__mail_open_tracking', $expand)) {
            $data['mail_open_tracking'] = MailOpenTrackingResource::collection($this->mailOpenTrackings);
        }

        return $data;
    }
}
