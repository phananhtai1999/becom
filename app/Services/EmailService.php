<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Email;

class EmailService extends AbstractService
{
    protected $modelClass = Email::class;

    /**
     * @param $perPage
     * @return mixed
     */
    public function indexMyEmail($perPage)
    {
        return $this->model->select('emails.*')
            ->join('websites', 'websites.uuid', '=', 'emails.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findMyEmailByKeyOrAbort($id)
    {
        $smtpAccount = $this->model->select('emails.*')
            ->join('websites', 'websites.uuid', '=', 'emails.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('emails.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $smtpAccount;
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function deleteMyEmailByKey($id)
    {
        $smtpAccount = $this->model->select('emails.*')
            ->join('websites', 'websites.uuid', '=', 'emails.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('emails.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $this->destroy($smtpAccount->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }
}
