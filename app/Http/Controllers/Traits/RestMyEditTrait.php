<?php

namespace App\Http\Controllers\Traits;

trait RestMyEditTrait
{
    public function editMy($id)
    {
        $request = app($this->editMyRequest);

        $model = $this->myService->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
