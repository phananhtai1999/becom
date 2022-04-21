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
            'mail_encryption' => $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'secret_key' => $this->secret_key,
            'website_uuid' => $this->website_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('smtp_account__website', $expand)) {
            $data['website'] = new WebsiteResource($this->website);
        }

        return $data;
    }
}
