<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestMyShowTrait
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMy($id)
    {
        $model = $this->myService->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
