<?php

namespace App\Rules;

use App\Services\MySectionTemplateService;
use App\Services\MyWebsiteService;

class CheckIsCanUseSectionTemplate
{
    public static function IsCanUseSectionTemplate($uuid, $websiteUuid = null)
    {
        return function ($attribute, $value, $fail) use ($uuid, $websiteUuid) {
            if (!$websiteUuid){
                if (!in_array($uuid, (new MySectionTemplateService())->getCanUseUuidsSectionTemplates()->toArray())){
                    $fail('This section template has been used for another website.');
                }
            }else{
                $website = (new MyWebsiteService())->findOneById($websiteUuid);
                if (!in_array($uuid, [$website->header_section_uuid, $website->footer_section_uuid]) and !in_array($uuid, (new MySectionTemplateService())->getCanUseUuidsSectionTemplates()->toArray())) {
                    $fail('This section template has been used for another website.');
                }
            }

        };
    }
}
