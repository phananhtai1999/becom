<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestMyDestroyTrait
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMy($id)
    {
        $model = $this->myService->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);

        $this->myService->destroy($model->uuid);

        return $this->sendOkJsonResponse();
    }
}
