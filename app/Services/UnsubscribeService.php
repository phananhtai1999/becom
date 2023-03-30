<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Unsubscribe;
use Illuminate\Support\Str;

class UnsubscribeService extends AbstractService
{
    protected $modelClass = Unsubscribe::class;

    public function createUnsubscribe($contactUuid = null)
    {
        do {
            $code = Str::random(10);
            $model = $this->findOneWhere(['code' => $code]);
        } while($model !== null);

        $model = $this->create([
            'contact_uuid' => $contactUuid,
            'code' => $code,
        ]);

        $lastUnsubscribes = $this->model->where('contact_uuid', $contactUuid)
            ->orderByDesc('created_at')->get();

        $this->model->destroy($lastUnsubscribes->slice(3)->pluck('code')->toArray());

        return $model;
    }
}
