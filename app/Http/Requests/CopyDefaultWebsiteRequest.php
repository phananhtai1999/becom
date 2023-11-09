<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Role;
use App\Models\SectionTemplate;
use App\Models\Website;
use App\Rules\CheckIsCanUseSectionTemplate;
use App\Rules\CheckUniqueSlugWebsitePageRule;
use App\Rules\CheckWebsiteDomainRule;
use App\Rules\CheckWebsitePagesRule;
use App\Rules\UniqueWebsitePage;
use Illuminate\Validation\Rule;

class CopyDefaultWebsiteRequest extends AbstractRequest
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
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'tracking_ids' => ['nullable', 'array'],
            'tracking_ids.*' => ['nullable', 'string', 'max:300'],
        ];

        if($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()){
            return $validate;
        }
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_EDITOR])->count()){
            $validate['publish_status'] = ['required', 'numeric',
                Rule::in(
                    Website::PENDING_PUBLISH_STATUS,
                    Website::DRAFT_PUBLISH_STATUS
                )
            ];
        }else{
            $validate['domain_uuid'] = ['required', 'numeric', Rule::exists('domains', 'uuid')->where(function ($q) {
                return $q->where('owner_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            }), CheckWebsiteDomainRule::uniqueDomain($this->id)];
        }

        return $validate;
    }
}
