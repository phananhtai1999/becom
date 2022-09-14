<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\MyContactQueryBuilder;

class MyContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = MyContactQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyContactByKeyOrAbort($id)
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
    public function deleteMyContactByKey($id)
    {
        $contact = $this->findMyContactByKeyOrAbort($id);

        return $this->destroy($contact->getKey());
    }
}
