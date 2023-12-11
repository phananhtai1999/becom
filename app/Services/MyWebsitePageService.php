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
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
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

    public function checkUniqueSlug($uuids)
    {
        $websitePages = $this->model->whereIn('uuid', $uuids)->get();
        $slugs = $websitePages->pluck('slug')->toArray();
        $uniqueSlugs = array_unique($slugs);

        return count($slugs) === count($uniqueSlugs);
    }

    public function getIsCanUseWebsitePages($request)
    {
        $isCanUseWebsitePages = $this->model
            ->leftJoin('website_website_page', 'website_website_page.website_page_uuid', 'website_pages.uuid')
            ->whereNull('website_website_page.website_page_uuid')
            ->where([
                ['website_pages.user_uuid', auth()->userId()],
                ['website_pages.app_id', auth()->appId()]
            ])->get()->pluck('uuid');
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereIn('uuid', $isCanUseWebsitePages)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
