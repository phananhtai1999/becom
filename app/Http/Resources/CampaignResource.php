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
            'number_email_per_date' => $this->number_email_per_date,
            'number_email_per_user' => $this->number_email_per_user,
            'status' => $this->status,
            'smtp_account_uuid' => $this->smtp_account_uuid,
            'website_uuid' => $this->website_uuid,
            'is_running' => $this->is_running,
            'was_finished' => $this->was_finished,
            'was_stopped_by_owner' => $this->was_stopped_by_owner,
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

        return $data;
    }
}
