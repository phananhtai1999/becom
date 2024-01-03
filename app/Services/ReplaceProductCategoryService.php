<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceProductCategoryService extends ReplaceChildrenProductCategoryService
{
    public function replaceListProductCategory($template) {
        preg_match('/data-product-category-count="(\d+)"/', $template, $categoryCount);
        $categoryCount = isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;

        //get orderby
        preg_match('/product-category-sort="(.*?)"/', $template, $sortName);
        preg_match('/product-category-sort-order="(.*?)"/', $template, $sortOrder);
        $categoriesData = $this->getChildrenByCategoryUuid(null, $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $childrenCategoryCount ?? 10);
        $categoriesData = $categoriesData['data']['data'];
        return preg_replace_callback('/<product-category-element.*?>(.*?)<\/product-category-element>/s', function ($matches) use ($categoriesData) {
            $categoryData = array_shift($categoriesData);
            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
            $matches[0] = $this->replacechildrenProductCategory($matches[0], $categoryData);
            if ($categoryData['product']) {
                $replaceProductService = new ReplaceProductService();
                $searchReplaceProductMap = $replaceProductService->searchReplaceMapForProduct($categoryData['product']);
                $matches[0] = Str::replace(array_keys($searchReplaceProductMap), $searchReplaceProductMap, $matches[0]);
            }

            return $matches[0];

        }, $template);
    }

    public function replaceCategoryInProduct($productTemplate, $category)
    {
        $searchReplaceMap = $this->searchReplaceMapForCategory($category);

        return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $productTemplate);
    }

    public function searchReplaceMapForCategory($articleCategory = null)
    {
        return [
            '{category.uuid}' => $articleCategory['uuid'] ?? null,
            '{category.slug}' => $articleCategory['slug'] ?? null,
            '{category.title}' => array_values($articleCategory['title'])[0] ?? null,
            '{category.image}' => $articleCategory['image'] ?? null,
            '{category.parent_uuid}' => $articleCategory['short_content'] ?? null,
        ];
    }
}
