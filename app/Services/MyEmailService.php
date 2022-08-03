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
        $smtpAccount = $this->model->select('emails.*')
            ->join('websites', 'websites.uuid', '=', 'emails.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['emails.uuid', $id]
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
    public function deleteMyEmailByKey($id)
    {
        $smtpAccount = $this->findMyEmailByKeyOrAbort($id);

        return $this->destroy($smtpAccount->getKey());
    }
}
