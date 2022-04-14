<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    /**
     * @param $perPage
     * @return mixed
     */
    public function indexMyMailTemplate($perPage)
    {
        return $this->model->select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findMyMailTemplateByKeyOrAbort($id)
    {
        $smtpAccount = $this->model->select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('mail_templates.uuid', $id)
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
    public function deleteMyMailTemplateByKey($id)
    {
        $smtpAccount = $this->model->select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('mail_templates.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $this->destroy($smtpAccount->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }
}
