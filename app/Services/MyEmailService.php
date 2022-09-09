<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Email;
use App\Models\QueryBuilders\MyEmailQueryBuilder;

class MyEmailService extends AbstractService
{
    protected $modelClass = Email::class;

    protected $modelQueryBuilderClass = MyEmailQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyEmailByKeyOrAbort($id)
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
    public function deleteMyEmailByKey($id)
    {
        $email = $this->findMyEmailByKeyOrAbort($id);

        return $this->destroy($email->getKey());
    }
}
