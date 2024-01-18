<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestShowByAppIdTrait
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $model = $this->service->findOneWhereOrFail([
            'uuid' => $id,
            'app_id' => auth()->appId()
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
