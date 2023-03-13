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
        $validate = [
            'key' => ['required', 'string', Rule::unique('configs')->whereNull('deleted_at')],
            'value' => ['nullable', 'string'],
            'type' => ['required', 'in:image,boolean,number,string'],
            'default_value' => ['nullable', 'string'],
            'group_id' => ['required', 'numeric', 'min:1', Rule::exists('groups', 'uuid')->whereNull('deleted_at')],
        ];

        if ($this->request->get('type') === 'image' || $this->request->get('type') === 'string') {

            $validate['value'] = ['nullable', 'string'];
        } elseif ($this->request->get('type') === 'boolean') {

            $validate['value'] = ['nullable', 'in:true,false'];
        } elseif ($this->request->get('type') === 'number') {

            $validate['value'] = ['nullable', 'numeric'];
        }

        return $validate;
    }
}
