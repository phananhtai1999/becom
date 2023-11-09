<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SectionTemplate;
use App\Rules\CheckIsCanUseSectionTemplate;
use App\Rules\CheckUniqueSlugWebsitePageRule;
use App\Rules\CheckWebsiteDomainRule;
use App\Rules\CheckWebsitePagesRule;
use App\Rules\UniqueWebsitePage;
use Illuminate\Validation\Rule;

class CopyMyDefaultWebsiteRequest extends AbstractRequest
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
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'domain_uuid' => ['required', 'numeric', Rule::exists('domains', 'uuid')->where(function ($q) {
                return $q->where('owner_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            }), CheckWebsiteDomainRule::uniqueDomain($this->id)],
            'tracking_ids' => ['nullable', 'array'],
            'tracking_ids.*' => ['nullable', 'string', 'max:300'],
        ];
    }
}
