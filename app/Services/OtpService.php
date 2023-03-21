<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Otp;

class OtpService extends AbstractService
{
    protected $modelClass = Otp::class;
}
