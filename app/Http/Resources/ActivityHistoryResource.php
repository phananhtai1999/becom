<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ActivityHistoryResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $statusType = !empty($this->content['status_type']) ? $this->content['status_type'] : null;
        $data = [
            'uuid' => $this->getKey(),
            'type' => $this->type,
            'type_id' => $this->type_id,
            'contact_uuid' => $this->contact_uuid,
            'date' => $this->date,
            'content' => ['content' => __('activity.'. $this->content['langkey'], $this->content), 'status_type' => $statusType],
            'render_body_mail_template' => $this->render_body_mail_template,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('activity_history__contact', $expand)) {
            $data['contact'] = new ContactResource($this->contact);
        }

        if (\in_array('activity_history__remind', $expand) && $this->type === 'remind') {
            $data['remind'] = new RemindResource($this->remind);
        }

        if (\in_array('activity_history__note', $expand) && $this->type === 'note') {
            $data['note'] = new NoteResource($this->note);
        }

        if (\in_array('activity_history__mail_sending_history', $expand) && $this->type != 'note' && $this->type != 'remind') {
            $data['mail_sending_history'] = new MailSendingHistoryResource($this->mailsendingHistory);
        }

        if (\in_array('activity_history__campaign', $expand) && $this->type != 'note' && $this->type != 'remind') {
            $data['campaign'] = new CampaignResource(optional($this->mailsendingHistory)->campaign);
        }

        if (\in_array('activity_history__mail_template', $expand) && $this->type != 'note' && $this->type != 'remind') {
            $data['mail_template'] = optional(new CampaignResource(optional($this->mailsendingHistory)->campaign))->mailTemplate;
        }
        return $data;
    }
}
