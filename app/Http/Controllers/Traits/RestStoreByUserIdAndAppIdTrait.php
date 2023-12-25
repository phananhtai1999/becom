<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestStoreByUserIdAndAppIdTrait
{
    /**
     * @return JsonResponse
     */
    public function storeMy()
    {
        $request = app($this->storeMyRequest);

        $model = $this->myService->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->myService->resourceToData($this->resourceClass, $model)
        );
    }
}
