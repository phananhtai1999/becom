<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ActivityHistory;
use App\Models\Note;
use App\Models\QueryBuilders\ActivityHistoryQueryBuilder;
use App\Models\Remind;
use Illuminate\Support\Str;

class ActivityHistoryService extends AbstractService
{
    protected $modelClass = ActivityHistory::class;

    protected $modelQueryBuilderClass = ActivityHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findActivityHistory($id)
    {
        return $this->findOneById($id);
    }

    /**
     * @param $uuid
     * @param $type
     * @param $require
     * @return string|null
     */
    public function renderBody($uuid, $type, $require)
    {
        if ($type == Note::NOTE_TYPE || $type == Remind::REMIND_TYPE) {
            return null;
        } else {
            $bodyMailTemplate = optional(optional(optional(optional($this->findActivityHistory($uuid))->mailsendingHistory)->campaign)->mailTemplate)->body;
            $contact = $this->findActivityHistory($uuid)->contact;

            if ($type === 'email') {
                if ($require == 'body') {
                    $html = str_replace("\n", '', $bodyMailTemplate);
                    if (preg_match("/^(.*<body[^>]*>)(.*)$/", $html, $matches)) {
                        $html = $matches[2];
                    }

                    $pattern = '/<style\b[^>]*>(.*?)<\/style>/si';
                    $renderBody = preg_replace($pattern, "", $html);
                    $renderBody = strip_tags($renderBody);
                    $renderBody = preg_replace('/\s+/', ' ', $renderBody);
                } elseif ($require == 'html') {
                    $renderBody = $bodyMailTemplate;
                }
            } else {
                $renderBody = strip_tags($bodyMailTemplate);
            }

            return $this->parseBody($contact, $renderBody);
        }
    }

    /**
     * @param $contact
     * @param $renderBody
     * @return string
     */
    public function parseBody($contact, $renderBody)
    {
        $toEmail = $contact->email ?? $contact;
        $contactFirstName = $contact->first_name ?? '';
        $contactMiddleName = $contact->middle_name ?? '';
        $contactLastName = $contact->last_name ?? '';
        $contactPhone = $contact->phone ?? '';
        $contactSex = $contact->sex ?? '';
        $contactDob = $contact->dob ?? '';
        $contactCountry = $contact->country ?? '';
        $contactCity = $contact->city ?? '';

        $search = [
            '{{to_email}}',
            '{{contact_first_name}}',
            '{{contact_middle_name}}',
            '{{contact_last_name}}',
            '{{contact_phone}}',
            '{{contact_sex}}',
            '{{contact_dob}}',
            '{{contact_country}}',
            '{{contact_city}}',
        ];
        $replace = [
            $toEmail, $contactFirstName, $contactMiddleName, $contactLastName, $contactPhone, $contactSex, $contactDob, $contactCountry, $contactCity
        ];

        return html_entity_decode(htmlspecialchars_decode(Str::replace($search, $replace, $renderBody)));
    }
}
