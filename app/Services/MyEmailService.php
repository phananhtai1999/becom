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

        $email = $this->model->select('emails.*')
            ->join('website_email', 'website_email.email_uuid', '=', 'emails.uuid')
            ->join('websites', 'websites.uuid', '=', 'website_email.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['emails.uuid', $id]
            ])->first();

        if (!empty($email)) {
            return $email;
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
        $email = $this->findMyEmailByKeyOrAbort($id);

        return $this->destroy($email->getKey());
    }
}
