<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ContactUnsubscribe;
use App\Models\QueryBuilders\ContactUnsubscribeQueryBuilder;

class ContactUnsubscribeService extends AbstractService
{
    protected $modelClass = ContactUnsubscribe::class;
    protected $modelQueryBuilderClass = ContactUnsubscribeQueryBuilder::class;

    /**
     * @param $contact
     * @param $unsubscribes
     * @return void
     */
    public function handleContactUnsubscribeByContact($contact, $unsubscribes): void
    {
        if ($contactUnsubscribe = $contact->contactUnsubscribe) {
            $unsubscribeByType = $contactUnsubscribe->unsubscribe;
            foreach ($unsubscribes as $value) {
                if (array_key_exists($value, $unsubscribeByType)) {
                    $unsubscribeByType[$value]++;
                }else{
                    $unsubscribeByType[$value] = 1;
                }
            }
            $this->update($contactUnsubscribe, [
                'unsubscribe' => $unsubscribeByType
            ]);
        }else{
            //array_fill_keys($unsubscribes, 1) chuyen gia tri trong $unsubscribes thanh key va gan 1 cho moi key do
            $contact->contactUnsubscribe()->create([
                'unsubscribe' => array_fill_keys($unsubscribes, 1)
            ]);
        }
    }
}
