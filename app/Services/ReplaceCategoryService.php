<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceCategoryService extends ReplaceChildrenCategoryService
{
    public function replaceListCategory($template, $websitePage) {
        $replaceArticleService = new ReplaceArticleService();
        $pattern = '/<category-list.*?>(.*?)<\/category-list>/s';

        return preg_replace_callback($pattern, function ($matches) use ($template, $replaceArticleService, $websitePage){
            $categoryCount = $this->searchCategoryCount($template);
            $sortName = $this->searchCategorySort($template);
            $sortOrder = $this->searchCategorySortOrder($template);
            $categoriesData = ArticleCategory::where('parent_uuid', null)->orderBy($sortName, $sortOrder)->paginate($categoryCount);
            return preg_replace_callback('/<category-element.*?>(.*?)<\/category-element>/s', function ($matchCategory) use ($categoriesData, $replaceArticleService, $websitePage) {
                $categoryData = $categoriesData->shift();
                if (!$categoryData) {

                    return $matchCategory[0];
                }
                $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
                $matchCategory[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchCategory[0]);
                $matchCategory[0] = $this->replacechildrenCategory($matchCategory[0], $categoryData);
                $childrenCategoriesUuid = ArticleCategory::where('parent_uuid', $categoryData->uuid)->first();
                if (!empty($childrenCategoriesUuid)) {
                    $matchCategory[0] = $replaceArticleService->replaceListArticle($matchCategory[0], $childrenCategoriesUuid, $websitePage);
                }

                return $matchCategory[0];

            }, $matches[0]);
        }, $template);
    }


    public function replaceListCategoryMenu($template)
    {
        $categoryCount = $this->searchCategoryCount($template);
        $sortName = $this->searchCategorySort($template);
        $sortOrder = $this->searchCategorySortOrder($template);

        $categoriesData = ArticleCategory::where('parent_uuid', null)->orderBy($sortName, $sortOrder)->paginate($categoryCount);

        return preg_replace_callback('/<category-element.*?>(.*?)<\/category-element>/s', function ($matches) use ($categoriesData) {
            $categoryData = $categoriesData->shift();
            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);

            return $matches[0];
        }, $template);
    }

    public function replaceCategoryInArticle($articleTemplate, $category) {
        $searchReplaceMap = $this->searchReplaceMapForCategory($category);

        return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $articleTemplate);
    }


    public function searchCategoryCount($template): int
    {
        preg_match('/data-category-count="(\d+)"/', $template, $categoryCount);

        return isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;
    }

    public function searchCategorySort($template)
    {
        preg_match('/category-sort="(.*?)"/', $template, $sortName);

        return $sortName[1] ?? 'created_at';
    }

    public function searchCategorySortOrder($template)
    {
        preg_match('/category-sort-order="(.*?)"/', $template, $sortOrder);

        return $sortOrder[1] ?? 'DESC';
    }


    public function searchReplaceMapForCategory($articleCategory = null)
    {
        if (!empty($articleCategory)) {
            return [
                '{category.uuid}' => $articleCategory->uuid ?? null,
                '{category.slug}' => $articleCategory->slug ?? null,
                '{category.title}' => $articleCategory->title ?? null,
                '{category.content}' => $articleCategory->content ?? null,
                '{category.feature_image}' => $articleCategory->feature_image ?? null,
                '{category.image}' => $articleCategory->image ?? null,
                '{category.keyword}' => $articleCategory->keyword ?? null,
                '{category.description}' => $articleCategory->description ?? null,
                '{category.short_content}' => $articleCategory->short_content ?? null,
            ];

        } else {
            return [
                '{category.uuid}' => 'This is category uuid sample',
                '{category.slug}' => 'This is category slug sample',
                '{category.title}' => 'This is category title sample',
                '{category.content}' => 'This is category content sample',
                '{category.feature_image}' => 'This is category feature_image sample',
                '{category.image}' => 'This is category image sample',
                '{category.keyword}' => 'This is category keyword sample',
                '{category.description}' => 'This is category description sample',
                '{category.short_content}' => 'This is category short_content sample',
            ];
        }

    }
    public function findListCategoryJson($components) {
        foreach ($components as $component) {
            if (isset($component->tagName) && $component->tagName == 'category-list') {
                $childrenCategoryCount = $component->attributes->{'data-category-count'} ?? 10;
                $sortName = $component->attributes->{'category-sort'} ?? 'created_at';
                $sortOrder = $component->attributes->{'category-sort-order'} ?? 'DESC';
                $component->components = $this->replaceCategoryElementJson($component->components, $childrenCategoryCount, $sortName, $sortOrder);
            }

            if (isset($component->components)) {
                $this->findListCategoryJson($component->components);
            }
        }
        return $components;
    }
    public function replaceCategoryElementJson($components, $childrenCategoryCount, $sortName, $sortOrder)
    {
        $categoriesData = ArticleCategory::orderBy($sortName, $sortOrder)->paginate($childrenCategoryCount);
        foreach ($components as $key => $categoryElement) {
            $categoryElementEncode = json_encode($categoryElement);
            $categoryData = $categoriesData->shift();
            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $components[$key] = json_decode(str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $categoryElementEncode));
            if ($categoryElement->components) {
                $articleService = new ReplaceArticleService();
                dd($articleService->replaceArticleJson($categoryElement->components));
            }
        }

        return $components;
    }
}
