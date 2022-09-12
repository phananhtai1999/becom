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
            ]);
        }

        return $contacts;
    }
}
