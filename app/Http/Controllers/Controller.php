<?php

namespace App\Http\Controllers;

use App\Abstracts\AbstractRestAPIController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends AbstractRestAPIController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function allApi() {

        return $this->sendOkJsonResponse(['data' => config('api')]);
    }
}
