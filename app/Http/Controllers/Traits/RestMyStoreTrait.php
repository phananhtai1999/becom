<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestMyStoreTrait
{
    /**
     * @return JsonResponse
     */
    public function storeMy()
    {
        $request = app($this->storeMyRequest);

        $model = $this->myService->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->myService->resourceToData($this->resourceClass, $model)
        );
    }
}
