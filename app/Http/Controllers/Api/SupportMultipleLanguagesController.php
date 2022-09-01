<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use Illuminate\Http\Request;

class SupportMultipleLanguagesController extends AbstractRestAPIController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCookie(Request $request)
    {
        $langs = $request->get('langs') ? $request->get('langs') : 'en';

        $listLanguages = ['vi', 'en', 'fr', 'ch'];

        if (!in_array($langs, $listLanguages)) {
            return $this->sendValidationFailedJsonResponse();
        }

        app()->setLocale($langs);

        return $this->sendOkJsonResponse()->withCookie(
            cookie('langs', $langs, 3600, null, null, true, false)
        );
    }
}
