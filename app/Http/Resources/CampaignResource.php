<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CampaignResource extends AbstractJsonResource
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
            'tracking_key' => $this->tracking_key,
            'mail_template_uuid' => $this->mail_template_uuid,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'status' => $this->status,
            'type' => $this->type,
            'send_type' => $this->send_type,
            'smtp_account_uuid' => $this->smtp_account_uuid,
            'website_uuid' => $this->website_uuid,
            'user_uuid' => $this->user_uuid,
            'was_finished' => $this->was_finished,
            'was_stopped_by_owner' => $this->was_stopped_by_owner,
            'number_credit_needed_to_start_campaign' => $this->number_credit_needed_to_start_campaign,
            'is_expired' => $this->is_expired,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('campaign__mail_template', $expand)) {
            $data['mail_template'] = new MailTemplateResource($this->mailTemplate);
        }

        if (\in_array('campaign__smtp_account', $expand)) {
            $data['smtp_account'] = new SmtpAccountResource($this->smtpAccount);
        }

        if (\in_array('campaign__website', $expand)) {
            $data['website'] = new WebsiteResource($this->website);
        }

        if (\in_array('campaign__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('campaign__contact_lists', $expand)) {
            $data['contact_lists'] = ContactListResource::collection($this->contactLists);
        }

        if (\in_array('campaign__campaign_scenario', $expand)) {
            $data['campaign_scenario'] = CampaignScenarioResource::collection($this->campaignsScenario);
        }

        return $data;
    }
}
