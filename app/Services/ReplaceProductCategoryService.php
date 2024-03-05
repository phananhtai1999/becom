<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceProductCategoryService extends ReplaceChildrenProductCategoryService
{
    public function replaceListProductCategory($template, ) {
        $replaceProductService = new ReplaceProductService();
        $pattern = '/<product-category-list.*?>(.*?)<\/product-category-list>/s';

        return preg_replace_callback($pattern, function ($matches) use ($template, $replaceProductService){
            $childrenCategoryCount = $this->searchCategoryCount($matches[0]);
            $sortName = $this->searchCategorySort($matches[0]);
            $sortOrder = $this->searchCategorySortOrder($matches[0]);
            $categoriesData = $this->getChildrenByCategoryUuid(null, $sortName, $sortOrder, $childrenCategoryCount);
            $categoriesData = $categoriesData['data']['data'];

            return preg_replace_callback('/<product-category-element.*?>(.*?)<\/product-category-element>/s', function ($matchProductCategory) use ($categoriesData, $replaceProductService) {
                $categoryData = array_shift($categoriesData);
                if (!$categoryData) {

                    return $matchProductCategory[0];
                }
                $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
                $matchProductCategory[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchProductCategory[0]);
                $matchProductCategory[0] = $this->replacechildrenProductCategory($matchProductCategory[0], $categoryData);
                $matchProductCategory[0] = $replaceProductService->replaceListProduct($matchProductCategory[0], $categoryData);

                return $matchProductCategory[0];
            }, $matches[0]);
        }, $template);

    }

    public function replaceListProductCategoryMenu($template)
    {
        $childrenCategoryCount = $this->searchCategoryCount($template);
        $sortName = $this->searchCategorySort($template);
        $sortOrder = $this->searchCategorySortOrder($template);

        $categoriesData = $this->getProductHeader($sortName, $sortOrder, $childrenCategoryCount);
        $categoriesData = $categoriesData['data']['data'];
        return preg_replace_callback('/<product-category-element.*?>(.*?)<\/product-category-element>/s', function ($matches) use ($categoriesData) {
            $categoryData = array_shift($categoriesData);
            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);

            return $matches[0];
        }, $template);
    }

    public function replaceCategoryInProduct($productTemplate, $category)
    {
        $searchReplaceMap = $this->searchReplaceMapForCategory($category);

        return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $productTemplate);
    }

    public function searchReplaceMapForCategory($articleCategory = null): array
    {
        return [
            '{product_category.uuid}' => $articleCategory['uuid'] ?? null,
            '{product_category.slug}' => $articleCategory['slug'] ?? null,
            '{product_category.title}' => array_values($articleCategory['titles'])[0] ?? null,
            '{product_category.image}' => $articleCategory['image'] ?? null,
            '{product_category.parent_uuid}' => $articleCategory['short_content'] ?? null,
        ];
    }

    public function searchCategoryCount($template): int
    {
        preg_match('/data-product-category-count="(\d+)"/', $template, $categoryCount);

        return isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;
    }

    public function searchCategorySort($template)
    {
        preg_match('/product-category-sort="(.*?)"/', $template, $sortName);

        return $sortName[1] ?? 'created_at';
    }

    public function searchCategorySortOrder($template)
    {
        preg_match('/product-category-sort-order="(.*?)"/', $template, $sortOrder);

        return $sortOrder[1] ?? 'desc';
    }
}
