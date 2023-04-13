<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmtpAccountResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'mail_mailer' => $this->mail_mailer,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'smtp_mail_encryption_uuid' => $this->smtp_mail_encryption_uuid,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'secret_key' => $this->secret_key,
            'send_project_uuid' => $this->send_project_uuid,
            'status' => $this->status,
            'publish' => $this->publish,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('smtp_account__send_project', $expand)) {
            $data['send_project'] = new SendProjectResource($this->sendProject);
        }

        if(\in_array('smtp_account__smtp_account_encryption', $expand)){
            $data['smtp_account_encryption'] = new SmtpAccountEncryptionResource($this->smtpAccountEncryption);
        }

        if (\in_array('smtp_account__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
