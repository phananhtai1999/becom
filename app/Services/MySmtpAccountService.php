<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MySmtpAccountQueryBuilder;
use App\Models\SmtpAccount;

class MySmtpAccountService extends AbstractService
{
    protected $modelClass = SmtpAccount::class;

    protected $modelQueryBuilderClass = MySmtpAccountQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMySmtpAccountByKeyOrAbort($id)
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
    public function deleteMySmtpAccountByKey($id)
    {
        $smtpAccount = $this->findMySmtpAccountByKeyOrAbort($id);

        return $this->destroy($smtpAccount->getKey());
    }
}
