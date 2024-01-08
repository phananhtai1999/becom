<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceCategoryService extends ReplaceChildrenCategoryService
{
    public function replaceListCategory($template) {
        $categoryCount = $this->searchCategoryCount($template);
        $sortName = $this->searchCategorySort($template);
        $sortOrder = $this->searchCategorySortOrder($template);
        
        $categoriesData = ArticleCategory::where('parent_uuid', null)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($categoryCount);
        return preg_replace_callback('/<category-element.*?>(.*?)<\/category-element>/s', function ($matches) use ($categoriesData) {
            $categoryData = $categoriesData->shift();

            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
            $matches[0] = $this->replacechildrenCategory($matches[0], $categoryData);

            $childrenCategoriesUuid = ArticleCategory::where('parent_uuid', $categoryData->uuid)->get()->pluck('uuid');
            $article = Article::whereIn('article_category_uuid', array_merge($childrenCategoriesUuid->toArray(), [$categoryData->uuid]))->orderBy('created_at', 'DESC')->first();
            if ($article) {
                $replaceArticleService = new ReplaceArticleService();
                $searchReplaceArticleMap = $replaceArticleService->searchReplaceMapForArticle($article);
                $matches[0] = Str::replace(array_keys($searchReplaceArticleMap), $searchReplaceArticleMap, $matches[0]);
            }

            return $matches[0];

        }, $template);
    }


    public function replaceListCategoryMenu($template)
    {
        $categoryCount = $this->searchCategoryCount($template);
        $sortName = $this->searchCategorySort($template);
        $sortOrder = $this->searchCategorySortOrder($template);

        $categoriesData = ArticleCategory::where('parent_uuid', null)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($categoryCount);
        return preg_replace_callback('/<category-element.*?>(.*?)<\/category-element>/s', function ($matches) use ($categoriesData) {
            $categoryData = $categoriesData->shift();

            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
            $matches[0] = $this->replacechildrenCategory($matches[0], $categoryData);

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

        return $sortName;
    }

    public function searchCategorySortOrder($template)
    {
        preg_match('/category-sort-order="(.*?)"/', $template, $sortOrder);

        return $sortOrder;
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
}
