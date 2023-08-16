<?php

namespace App\Rules;

use App\Services\BusinessManagementService;
use App\Services\DomainService;
use Illuminate\Contracts\Validation\Rule;

class CheckActiveMailBoxRule implements Rule
{
    private $domainUuid;

    /**
     * @param $domainUuid
     */
    public function __construct($domainUuid)
    {
        $this->domainUuid = $domainUuid;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $business = (new BusinessManagementService())->findOneWhere([['owner_uuid', auth()->user()->getkey()]]);
        $domain = (new DomainService())->findOneWhere([
            ['uuid', $this->domainUuid],
            ['business_uuid', optional($business)->uuid],
        ]);

        if ($business && $domain) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute invalid.';
    }
}
