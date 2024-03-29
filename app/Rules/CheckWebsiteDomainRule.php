<?php

namespace App\Rules;

use App\Services\MyWebsiteService;

class CheckWebsiteDomainRule
{
    public static function uniqueDomain($uuid)
    {
        return function ($attribute, $value, $fail) use ($uuid) {
            //Do not allow domain to belong to many websites
            $website = (new MyWebsiteService())->findOneWhere([
                ['domain_uuid', $value],
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()],
                ['uuid', '!=', $uuid],
            ]);
            if ($website) {
                $fail('The domain already belongs to another website.');
            }
        };
    }
}
