<?php

namespace App\Services;

use App\Abstracts\AbstractService;

class FileVerificationService extends AbstractService
{
    /**
     * @return string
     */
    public function verificationFileName()
    {
        $nameApp = preg_replace('/\s+/', '-', trim(config('app.name')));
        $verificationFileName = "$nameApp-verify-code.html";

        return $verificationFileName;
    }
}
