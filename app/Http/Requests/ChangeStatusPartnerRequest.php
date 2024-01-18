<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Services\PartnerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;

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
        $validate =  [
            'partner_uuid' => ['required', Rule::exists('partners','uuid')->whereNull('deleted_at')],
//            'user_uuid' => [Rule::exists('partners', 'user_uuid')->where(function ($query) {
//                $query->where('uuid', $this->request->get('partner_uuid'));
//            })],
            'publish_status' => ['required', 'in:active,block,pending', Rule::unique('partners','publish_status')->where(function ($query) {
                $query->where('uuid', $this->request->get('partner_uuid'));
            })],
        ];

        if ($this->request->get('publish_status') === 'active') {
            $partner = (new PartnerService())->findOneById($this->request->get('partner_uuid'));
            if (!$partner->user_uuid){
                $validate['partner_role'] = ['required', 'string'];
            }
        }

        return $validate;
    }
}
