<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceProductCategoryService extends ReplaceChildrenCategoryService
{
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
