<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyWebsiteQueryBuilder;
use App\Models\Website;

class MyWebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = MyWebsiteQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyWebsite($id)
    {
        return  $this->findOneWhereOrFail([
            ['from_user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyWebsite($id)
    {
        $website = $this->showMyWebsite($id);

        return $this->destroy($website->getKey());
    }
}
