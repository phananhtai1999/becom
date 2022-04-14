<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Website;

class WebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    /**
     * @param $perPage
     * @return mixed
     */
    public function indexMyWebsite($perPage)
    {
        return $this->model->where('user_uuid', auth()->user()->getkey())
            ->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showMyWebsite($id)
    {
        return $this->model->where('user_uuid', auth()->user()->getkey())
            ->where('uuid', $id)
            ->firstOrFail();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyWebsite($id)
    {
        $website = $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);

        return $this->destroy($website->getKey());
    }
}
