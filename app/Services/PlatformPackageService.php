<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PlatformPackage;

class PlatformPackageService extends AbstractService
{
    protected $modelClass = PlatformPackage::class;
}
