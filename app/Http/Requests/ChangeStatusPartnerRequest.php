<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusPartnerRequest extends AbstractRequest
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
        return [
            'partner_uuid' => ['required', Rule::exists('partners','uuid')->whereNull('deleted_at')],
            'publish_status' => ['required', 'in:active,block,pending', Rule::unique('partners','publish_status')->where(function ($query) {
                $query->where('uuid', $this->request->get('partner_uuid'));
            })]
        ];
    }
}
