<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestShowByUserIdAndAppIdTrait
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMy($id)
    {
        $model = $this->service->findOneWhereOrFailByUserUuidAndAppId($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
