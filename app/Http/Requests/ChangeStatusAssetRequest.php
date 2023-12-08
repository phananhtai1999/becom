<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Asset;
use App\Models\Role;
use App\Services\ConfigService;
use Illuminate\Validation\Rule;

class ChangeStatusAssetRequest extends AbstractRequest
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
          'status' => ['required', Rule::in(Asset::REJECT_STATUS, Asset::PUBLISH_STATUS, Asset::PENDING_STATUS, Asset::DRAFT_STATUS)]
        ];

        //Check role editor
        if (!(new ConfigService())->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN]))
        {
            $validate['status'] = ['required', Rule::in(Asset::PENDING_STATUS, Asset::DRAFT_STATUS)];
        }

        if ($this->request->get('status') == Asset::REJECT_STATUS){
            $validate['reject_reason'] = ['required', 'string'];
        }
        return $validate;
    }
}
