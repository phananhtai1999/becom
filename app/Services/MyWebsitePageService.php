<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyWebsitePageQueryBuilder;
use App\Models\WebsitePage;

class MyWebsitePageService extends AbstractService
{
    protected $modelClass = WebsitePage::class;

    protected $modelQueryBuilderClass = MyWebsitePageQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyWebsitePageByUuid($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyWebsitePageByUuid($id)
    {
        $websitePage = $this->showMyWebsitePageByUuid($id);

        return $this->destroy($websitePage->getKey());
    }
}
