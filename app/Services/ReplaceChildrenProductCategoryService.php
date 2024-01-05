<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceChildrenProductCategoryService extends ShopService
{
    function replaceChildrenProductCategory($categoryTemplates, $categoryData)
    {
        preg_match('/data-children-product-category-count="(\d+)"/', $categoryTemplates, $childrenCategoryCount);
        $childrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$childrenCategoryCount[1] : 10;
        //get orderby
        preg_match('/children-product-category-sort="(.*?)"/', $categoryTemplates, $sortName);
        preg_match('/children-product-category-sort-order="(.*?)"/', $categoryTemplates, $sortOrder);

        $childrenCategoriesData = $this->getChildrenByCategoryUuid($categoryData['uuid'], $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $childrenCategoryCount ?? 10);
        $childrenCategoriesData = $childrenCategoriesData['data']['data'];

        return preg_replace_callback('/<children-product-category-element.*?>(.*?)<\/children-product-category-element>/s', function ($childMatches) use ($childrenCategoriesData) {
            $childrenCategoryData = array_shift($childrenCategoriesData);
            if (!$childrenCategoryData) {
                return $childMatches[0];
            }

            if ($childrenCategoryData['product']) {
                $replaceProduct = new ReplaceProductService();
                $searchReplaceProductMap = $replaceProduct->searchReplaceMapForProduct($childrenCategoryData['product']);
                $childMatches[0] = Str::replace(array_keys($searchReplaceProductMap), $searchReplaceProductMap, $childMatches[0]);
            }
            $childSearchReplaceMap = $this->searchReplaceMapForChildrenCategory($childrenCategoryData);
            $childMatches[0] = str_replace(array_keys($childMatches), $childSearchReplaceMap, $childMatches[0]);
//            $childMatches[0] = $this->replaceGrandChildrenCategory($childMatches[0], $childrenCategoryData);

            return $childMatches[0];
        }, $categoryTemplates);
    }

    function replaceGrandChildrenCategory($matches, $childrenCategoryData)
    {
        preg_match('/data-grand-children-product-category-count="(\d+)"/', $matches, $grandChildrenCategoryCount);
        $grandChildrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$grandChildrenCategoryCount[1] : 10;

        //get orderby
        preg_match('/grand-children-product-category-sort="(.*?)"/', $matches, $sortName);
        preg_match('/grand-children-product-category-sort-order="(.*?)"/', $matches, $sortOrder);
        $grandChildrenCategoriesData = $this->getChildrenByCategoryUuid($childrenCategoryData['uuid'], $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $childrenCategoryCount ?? 10);
        $grandChildrenCategoriesData = $grandChildrenCategoriesData['data'];

        return preg_replace_callback('/<grand-children-product-category-element.*?>(.*?)<\/grand-children-product-category-element>/s', function ($grandChildMatches) use ($grandChildrenCategoriesData) {
            $grandChildrenCategoryData = $grandChildrenCategoriesData->shift();
            $grandChildSearchReplaceMap = $this->searchReplaceMapForGrandChildrenCategory($grandChildrenCategoryData);
            return str_replace(array_keys($grandChildSearchReplaceMap), $grandChildSearchReplaceMap, $grandChildMatches[0]);
        }, $matches);
    }

    private function searchReplaceMapForChildrenCategory($childrenCategory = null)
    {
        return [
            '{children_product_category.uuid}' => $childrenCategory['uuid'] ?? null,
            '{children_product_category.slug}' => $childrenCategory['slug'] ?? null,
            '{children_product_category.title}' => $childrenCategory['title'] ?? null,
            '{children_product_category.image}' => $childrenCategory['image'] ?? null,
            '{children_product_category.parent_uuid}' => $childrenCategory['parent_uuid'] ?? null,
        ];
    }

    private function searchReplaceMapForGrandChildrenCategory($grandChildrenCategory = null)
    {
        return [
            '{grand_children_product_category.uuid}' => $grandChildrenCategory['uuid'] ?? null,
            '{grand_children_product_category.slug}' => $grandChildrenCategory['slug'] ?? null,
            '{grand_children_product_category.title}' => $grandChildrenCategory['title'] ?? null,
            '{grand_children_product_category.image}' => $grandChildrenCategory['image'] ?? null,
            '{grand_children_product_category.parent_uuid}' => $grandChildrenCategory['parent_uuid'] ?? null,
        ];
    }
}
