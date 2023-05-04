<?php

namespace App\Http\Controllers\Traits;

trait RestMyEditTrait
{
    public function edit($id)
    {
        $request = app($this->editMyRequest);

        $model = $this->myService->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
