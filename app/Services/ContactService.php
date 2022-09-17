<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\ContactQueryBuilder;

class ContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = ContactQueryBuilder::class;

    /**
     * @param $file
     * @return array
     */
    public function importJsonFile($file)
    {
        $getFileContents = json_decode(file_get_contents($file));
        $contacts = [];

        foreach ($getFileContents as $content)
        {
            $contacts [] = $this->model->create([
                'email' => $content->email,
                'last_name' => $content->last_name,
                'first_name' => $content->first_name,
                'middle_name' => $content->middle_name,
                'phone' => $content->phone,
                'sex' => $content->sex,
                'dob' => $content->dob,
                'city' => $content->city,
                'country' => $content->country,
                'user_uuid' => auth()->user()->getKey()
            ]);
        }

        return $contacts;
    }

    /**
     * @param $model
     * @return array|void
     */
    public function findContactListKeyByContact($model)
    {
        $contactLists = $model->contactLists()->get();

        if (empty($contactLists)) {

            return [];
        } else {
            foreach ($contactLists as $contactList) {
                $contactListUuid[] = $contactList->uuid;

                return $contactListUuid;
            }
        }
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getContactsSendEmail($campaignUuid)
    {
         return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)
             ->whereIn('contacts.uuid', function ($query) {
                 $query->selectRaw('MIN(uuid)')->from('contacts')->groupBy('email');
             })->get();
    }
}
