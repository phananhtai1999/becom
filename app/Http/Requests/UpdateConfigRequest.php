<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Config;
use Illuminate\Validation\Rule;

class UpdateConfigRequest extends AbstractRequest
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
        $validate = [
            'key' => ['string', 'unique:configs,key,' . $this->id . ',uuid,deleted_at,NULL'],
            'value' => ['nullable', 'string'],
            'type' => ['in:image,boolean,numeric,string,smtp_account,s3,mailbox'],
            'status' => ['in:public,system,private'],
            'default_value' => ['nullable', 'string'],
            'group_id' => ['numeric', 'min:1', Rule::exists('groups', 'uuid')->whereNull('deleted_at')],
        ];

        if (in_array($this->request->get('key'), ['s3_system', 's3_user', 's3_website'])) {

            $validate['type'] = ['required', 'in:s3'];
        }

        if (in_array($this->request->get('key'), [Config::CONFIG_MAILBOX_MX, Config::CONFIG_MAILBOX_DKIM, Config::CONFIG_MAILBOX_DMARC])) {
            $validate['value'] = ['required', 'array'];
            $validate['value.name'] = ['required', 'string'];
            $validate['value.type'] = ['required', 'string', 'in:TXT'];
            $validate['value.value'] = ['required', 'string'];
            $validate['value.priority'] = ['nullable', 'in:NULL'];
            $validate['type'] = ['required', 'in:mailbox'];
            if ($this->request->get('key') === Config::CONFIG_MAILBOX_MX) {
                $validate['value.type'] = ['required', 'string', 'in:MX'];
                $validate['value.priority'] = ['required', 'numeric'];
            }
        }

        if ($this->request->get('type') === 'image' || $this->request->get('type') === 'string') {

            $validate['value'] = ['nullable', 'string'];
        } elseif ($this->request->get('type') === 'boolean') {

            $validate['value'] = ['nullable', 'boolean'];
        } elseif ($this->request->get('type') === 'numeric') {

            $validate['value'] = ['nullable', 'numeric'];
        } elseif ($this->request->get('type') === 'smtp_account') {

            $validate['value'] = ['array'];
            $validate['value.mail_host'] = ['string'];
            $validate['value.mail_port'] = ['string'];
            $validate['value.mail_username'] = ['string'];
            $validate['value.mail_password'] = ['string'];
            $validate['value.mail_encryption'] = ['string'];
            $validate['value.mail_from_address'] = ['string'];
            $validate['value.mail_from_name'] = ['string'];
        } elseif ($this->request->get('type') === 's3') {

            $validate['key'] = ['string', 'in:s3_system,s3_user,s3_website', Rule::unique('configs')->ignore($this->id, 'uuid')->whereNull('deleted_at')];
            $validate['value'] = ['required', 'array'];
            $validate['value.driver'] = ['required', 'string', 'in:s3'];
            $validate['value.key'] = ['required', 'string'];
            $validate['value.secret'] = ['required', 'string'];
            $validate['value.region'] = ['required', 'string'];
            $validate['value.bucket'] = ['required', 'string'];
            $validate['value.url'] = ['nullable', 'string'];
            $validate['value.endpoint'] = ['required', 'string'];
            $validate['value.use_path_style_endpoint'] = ['nullable', 'boolean'];
        }  elseif ($this->request->get('type') === 'mailbox') {

            $validate['key'] = ['required', 'string', Rule::in(Config::CONFIG_MAILBOX_MX, Config::CONFIG_MAILBOX_DKIM, Config::CONFIG_MAILBOX_DMARC), Rule::unique('configs')->ignore($this->id,'uuid')->whereNull('deleted_at')];
        }

        return $validate;
    }
}
