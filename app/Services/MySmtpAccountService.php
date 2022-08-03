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
        $smtpAccount = $this->model->select('smtp_accounts.*')
            ->join('websites', 'websites.uuid', '=', 'smtp_accounts.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['smtp_accounts.uuid', $id]
            ])->first();

        if (!empty($smtpAccount)) {
            return $smtpAccount;
        } else {
            abort(403, 'Unauthorized.');
        }
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
