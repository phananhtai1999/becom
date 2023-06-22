<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestMyDestroyTrait;
use App\Http\Controllers\Traits\RestMyShowTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyWebsiteRequest;
use App\Http\Requests\UpdateMyWebsiteRequest;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\WebsiteResourceCollection;
use App\Models\Website;
use App\Services\ArticleService;
use App\Services\AssetService;
use App\Services\FormService;
use App\Services\MailTemplateService;
use App\Services\MyWebsiteService;
use App\Services\SectionTemplateService;
use App\Services\WebsitePageService;
use App\Services\WebsiteService;
use Carbon\Carbon;

class EditorChartController extends AbstractRestAPIController
{

    protected $websiteService;
    protected $sectionTemplateService;

    protected $websitePageService;

    protected $formService;

    protected $articleService;

    protected $assetService;

    protected $mailTemplateService;

    public function __construct(
        WebsiteService $websiteService,
        SectionTemplateService $sectionTemplateService,
        WebsitePageService $websitePageService,
        FormService $formService,
        ArticleService $articleService,
        AssetService $assetService,
        MailTemplateService $mailTemplateService

    )
    {
        $this->websiteService = $websiteService;
        $this->sectionTemplateService = $sectionTemplateService;
        $this->websitePageService = $websitePageService;
        $this->formService = $formService;
        $this->articleService = $articleService;
        $this->assetService = $assetService;
        $this->mailTemplateService = $mailTemplateService;
    }
    public function editorWebsiteChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $type = $request->get('type');
        $result = [];

        if(!$type || $type == 'section_template'){
            $dataSection = $this->sectionTemplateService->editorSectionChart($groupBy, $startDate, $endDate);
            $totalSection = $this->sectionTemplateService->totalEditorSectionChart($startDate, $endDate);
            $result = [
                'data' => $dataSection,
                'total' => $totalSection
            ];
        }

        if(!$type || $type == 'website_page'){
            $dataWebsitePage = $this->websitePageService->editorWebsitePageChart($groupBy, $startDate, $endDate);
            $totalWebsitePage = $this->websitePageService->totalEditorWebsitePageChart($startDate, $endDate);
            $result = [
                'data' => $dataWebsitePage,
                'total' => $totalWebsitePage
            ];
        }

        if(!$type || $type == 'form'){
            $dataForm = $this->formService->editorFormChart($groupBy, $startDate, $endDate);
            $totalForm = $this->formService->totalEditorFormChart($startDate, $endDate);
            $result = [
                'data' => $dataForm,
                'total' => $totalForm
            ];
        }

        if(!$type){
            $mergedData = collect($dataSection)->concat(collect($dataWebsitePage))->concat(collect($dataForm));
            $resultData = $mergedData->groupBy('label')->map(function ($items, $key) {
                return [
                    'label' => $key,
                    'approve' => $items->sum('approve'),
                    'pending' => $items->sum('pending'),
                    'reject' => $items->sum('reject'),
                ];
            })->values();

            $mergedTotal = [$totalSection, $totalWebsitePage, $totalForm];
            $resultTotal = [
                "approve" => 0,
                "pending" => 0,
                "reject" => 0
            ];
            foreach ($mergedTotal as $item){
                $resultTotal['approve'] += $item['approve'];
                $resultTotal['pending'] += $item['pending'];
                $resultTotal['reject'] += $item['reject'];
            }

            $result = [
                'data' => $resultData,
                'total' => $resultTotal
            ];
        }

        return $this->sendOkJsonResponse($result);
    }

    public function editorAllChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        $dataSection = $this->sectionTemplateService->editorSectionChart($groupBy, $startDate, $endDate);
        $totalSection = $this->sectionTemplateService->totalEditorSectionChart($startDate, $endDate);

        $dataWebsitePage = $this->websitePageService->editorWebsitePageChart($groupBy, $startDate, $endDate);
        $totalWebsitePage = $this->websitePageService->totalEditorWebsitePageChart($startDate, $endDate);

        $dataForm = $this->formService->editorFormChart($groupBy, $startDate, $endDate);
        $totalForm = $this->formService->totalEditorFormChart($startDate, $endDate);

        $dataMailTemplate = $this->mailTemplateService->editorMailTemplateChart($groupBy, $startDate, $endDate);
        $totalMailTemplate = $this->mailTemplateService->totalEditorMailTemplateChart($startDate, $endDate);

        $dataArticle = $this->articleService->editorArticleChart($groupBy, $startDate, $endDate);
        $totalArticle = $this->articleService->totalEditorArticleChart($startDate, $endDate);

        $dataAsset = $this->assetService->editorAssetChart($groupBy, $startDate, $endDate);
        $totalAsset = $this->assetService->totalEditorAssetChart($startDate, $endDate);

        $mergedData = collect($dataSection)->concat(collect($dataWebsitePage))->concat(collect($dataForm))
            ->concat(collect($dataMailTemplate))->concat(collect($dataArticle))->concat(collect($dataAsset));
        $resultData = $mergedData->groupBy('label')->map(function ($items, $key) {
            return [
                'label' => $key,
                'approve' => $items->sum('approve'),
                'pending' => $items->sum('pending'),
                'reject' => $items->sum('reject'),
            ];
        })->values();

        $mergedTotal = [$totalSection, $totalWebsitePage, $totalForm, $totalMailTemplate, $totalArticle, $totalAsset];

        $resultTotal = [
            "approve" => 0,
            "pending" => 0,
            "reject" => 0
        ];
        foreach ($mergedTotal as $item){
            $resultTotal['approve'] += $item['approve'];
            $resultTotal['pending'] += $item['pending'];
            $resultTotal['reject'] += $item['reject'];
        }

        $result = [
            'data' => $resultData,
            'total' => $resultTotal
        ];

        return $this->sendOkJsonResponse($result);
    }
}
