<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestDestroyByUserIdAndAppIdTrait
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMy($id)
    {
        $this->service->destroyByUserIdAndAppId($id);

        return $this->sendOkJsonResponse();
    }
}
