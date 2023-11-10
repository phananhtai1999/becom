<?php

namespace App\Rules;

use App\Models\Role;
use App\Services\MySectionTemplateService;
use App\Services\MyWebsiteService;
use App\Services\SectionTemplateService;

class CheckIsCanUseSectionTemplate
{
    public static function IsCanUseSectionTemplate($uuid, $websiteUuid = null)
    {
        return function ($attribute, $value, $fail) use ($uuid, $websiteUuid) {
            if (auth()->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])){
                $uuidsSectionTemplatesCanUsed = (new SectionTemplateService())->getCanUseUuidsSectionTemplates()->toArray();
            }else{
                $uuidsSectionTemplatesCanUsed = (new MySectionTemplateService())->getCanUseUuidsSectionTemplates()->toArray();
            }
            if (!$websiteUuid){
                if (!in_array($uuid, $uuidsSectionTemplatesCanUsed)){
                    $fail('This section template has been used for another website.');
                }
            }else{
                $website = (new MyWebsiteService())->findOneById($websiteUuid);
                if (!in_array($uuid, [$website->header_section_uuid, $website->footer_section_uuid]) and !in_array($uuid, $uuidsSectionTemplatesCanUsed)) {
                    $fail('This section template has been used for another website.');
                }
            }

        };
    }
}
