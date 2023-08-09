<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class ConfigRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $configMailbox = config('mailbox.mailbox_mx_domain');
        $validate = [
            'key' => ['required', 'string', Rule::unique('configs')->whereNull('deleted_at')],
            'value' => ['nullable', 'string'],
            'type' => ['required', 'in:image,boolean,numeric,string,smtp_account,s3,mailbox'],
            'status' => ['required', 'in:public,system,private'],
            'default_value' => ['nullable', 'string'],
            'group_id' => ['required', 'numeric', 'min:1', Rule::exists('groups', 'uuid')->whereNull('deleted_at')],
        ];

        if (in_array($this->request->get('key'), ['s3_system', 's3_user', 's3_website'])) {

            $validate['type'] = ['required', 'in:s3'];
        }

        if (in_array($this->request->get('key'), ['mailbox_mx_domain', 'mailbox_dmarc_domain', 'mailbox_dkim_domain'])) {
            $validate['value'] = ['required', 'array'];
            $validate['value.record'] = ['required', 'string'];
            $validate['value.type'] = ['nullable', 'string', 'in:TXT'];
            $validate['value.value'] = ['required', 'string'];
            $validate['type'] = ['required', 'in:mailbox'];
            if ($this->request->get('key') === 'mailbox_mx_domain') {
                $validate['value.record'] = ['required', 'string', "in:$configMailbox"];
                $validate['value.type'] = ['nullable', 'string', 'in:MX'];
            }
        }

        if ($this->request->get('type') === 'image' || $this->request->get('type') === 'string') {

            $validate['value'] = ['nullable', 'string'];
        } elseif ($this->request->get('type') === 'boolean') {

            $validate['value'] = ['nullable', 'boolean'];
        } elseif ($this->request->get('type') === 'numeric') {

            $validate['value'] = ['nullable', 'numeric'];
        } elseif ($this->request->get('type') === 'smtp_account') {

            $validate['value'] = ['required', 'array'];
            $validate['value.mail_host'] = ['required', 'string'];
            $validate['value.mail_port'] = ['required', 'string'];
            $validate['value.mail_username'] = ['required', 'string'];
            $validate['value.mail_password'] = ['required', 'string'];
            $validate['value.mail_encryption'] = ['required', 'string'];
            $validate['value.mail_from_address'] = ['required', 'string'];
            $validate['value.mail_from_name'] = ['required', 'string'];
        } elseif ($this->request->get('type') === 's3') {

            $validate['key'] = ['required', 'string', 'in:s3_system,s3_user,s3_website', Rule::unique('configs')->whereNull('deleted_at')];
            $validate['value'] = ['required', 'array'];
            $validate['value.driver'] = ['required', 'string', 'in:s3'];
            $validate['value.key'] = ['required', 'string'];
            $validate['value.secret'] = ['required', 'string'];
            $validate['value.region'] = ['required', 'string'];
            $validate['value.bucket'] = ['required', 'string'];
            $validate['value.url'] = ['nullable', 'string'];
            $validate['value.endpoint'] = ['required', 'string'];
            $validate['value.use_path_style_endpoint'] = ['nullable', 'boolean'];
        } elseif ($this->request->get('type') === 'mailbox') {

            $validate['key'] = ['required', 'string', 'in:mailbox_mx_domain,mailbox_dmarc_domain,mailbox_dkim_domain', Rule::unique('configs')->whereNull('deleted_at')];
        }

        return $validate;
    }
}
