<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Services\LanguageService;
use Illuminate\Http\Request;

class SupportMultipleLanguagesController extends AbstractRestAPIController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCookie(Request $request)
    {
        $lang = $request->get('lang') ? $request->get('lang') : 'en';

        $languagesSupport = app(LanguageService::class)->languagesSupport();

        if (!in_array($lang, $languagesSupport)) {
            return $this->sendValidationFailedJsonResponse();
        }

        app()->setLocale($lang);

        return $this->sendOkJsonResponse()->withCookie(
            cookie('lang', $lang, 3600, null, null, true, false)
        );
    }
}
