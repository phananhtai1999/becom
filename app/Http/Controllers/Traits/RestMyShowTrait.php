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
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
