<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\FooterTemplate;
use App\Models\QueryBuilders\FooterTemplateQueryBuilder;
use AWS\CRT\Log;

class FooterTemplateService extends AbstractService
{
    protected $modelClass = FooterTemplate::class;

    protected $modelQueryBuilderClass = FooterTemplateQueryBuilder::class;

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getFooterTemplatesWithTopDefault($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return FooterTemplateQueryBuilder::searchQuery($search, $searchBy)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns, $pageName, $page);
    }

    public function changeIsDefaultFooterTemplateByType($type)
    {
        $footerTemplate = $this->findOneWhere([
           'type' => $type,
           'is_default' => true,
        ]);

        if ($footerTemplate) {
            $footerTemplate->update([
                'is_default' => false
            ]);
        }
    }

    public function changeActiveByFooterTemplateByType($type)
    {
        $footerTemplate = $this->findOneWhere([
            'type' => $type,
            'active_by_uuid' => auth()->user()->getKey(),
            'user_uuid' => auth()->user()->getKey()
        ]);
        if ($footerTemplate) {
            $footerTemplate->update([
                'active_by_uuid' => null
            ]);
        }
    }

    public function getFooterTemplateForSendCampaignByType($type, $user)
    {
        if ($user->can_remove_footer_template) {
            $footerTemplate = $this->findOneWhere([
                'type' => $type,
                'active_by_uuid' => $user->uuid,
            ]);
        }else{
            $footerTemplate = $this->findOneWhere([
                'type' => $type,
                'is_default' => true,
            ]);
        }

        return $footerTemplate;
    }
}
