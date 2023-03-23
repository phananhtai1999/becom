<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
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
            'key' => ['string', 'unique:configs,key,'.$this->id .',uuid,deleted_at,NULL'],
            'value' => ['nullable', 'string'],
            'type' => ['in:image,boolean,numeric,string,smtp_account'],
            'status' => ['in:public,system,private'],
            'default_value' => ['nullable', 'string'],
            'group_id' => ['numeric', 'min:1', Rule::exists('groups', 'uuid')->whereNull('deleted_at')],
        ];

        if ($this->request->get('type') === 'image' || $this->request->get('type') === 'string') {

            $validate['value'] = ['nullable', 'string'];
        } elseif ($this->request->get('type') === 'boolean') {

            $validate['value'] = ['nullable', 'boolean'];
        } elseif ($this->request->get('type') === 'numeric') {

            $validate['value'] = ['nullable', 'numeric'];
        }elseif ($this->request->get('type') === 'smtp_account') {

            $validate['value'] = ['array'];
            $validate['value.mail_host'] = ['string'];
            $validate['value.mail_port'] = ['string'];
            $validate['value.mail_username'] = ['string'];
            $validate['value.mail_password'] = ['string'];
            $validate['value.mail_encryption'] = ['string'];
            $validate['value.mail_from_address'] = ['string'];
            $validate['value.mail_from_name'] = ['string'];
        }

        return $validate;
    }
}
