<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;

class ReplaceChildrenCategoryService
{
    function replaceChildrenCategory($categoryTemplates, $categoryData)
    {
        preg_match('/data-children-category-count="(\d+)"/', $categoryTemplates, $childrenCategoryCount);
        $childrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$childrenCategoryCount[1] : 10;
        //get orderby
        preg_match('/children-category-sort="(.*?)"/', $categoryTemplates, $sortName);
        preg_match('/children-category-sort-order="(.*?)"/', $categoryTemplates, $sortOrder);
        $childrenCategoriesData = ArticleCategory::where('parent_uuid', $categoryData->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($childrenCategoryCount);
        return preg_replace_callback('/<children-category-element.*?>(.*?)<\/children-category-element>/s', function ($childMatches) use ($childrenCategoriesData) {
            $childrenCategoryData = $childrenCategoriesData->shift();
            if (!$childrenCategoryData) {
                return $childMatches[0];
            }
            $childrenCategoriesUuid = ArticleCategory::where('parent_uuid', $childrenCategoryData->uuid)->get()->pluck('uuid');
            $article = Article::whereIn('article_category_uuid', array_merge($childrenCategoriesUuid->toArray(), [$childrenCategoryData->uuid]))->orderBy('created_at', 'DESC')->first();
            if ($article) {
                $replaceArticle = new ReplaceArticleService();
                $searchReplaceArticleMap = $replaceArticle->searchReplaceMapForArticle($article);
                $childMatches[0] = Str::replace(array_keys($searchReplaceArticleMap), $searchReplaceArticleMap, $childMatches[0]);
            }
            $childSearchReplaceMap = $this->searchReplaceMapForChildrenCategory($childrenCategoryData);
            $childMatches[0] = str_replace(array_keys($childMatches), $childSearchReplaceMap, $childMatches[0]);
            $childMatches[0] = $this->replaceGrandChildrenCategory($childMatches[0], $childrenCategoryData);

            return $childMatches[0];
        }, $categoryTemplates);
    }

    function replaceGrandChildrenCategory($matches, $childrenCategoryData)
    {
        preg_match('/data-grand-children-category-count="(\d+)"/', $matches, $grandChildrenCategoryCount);
        $grandChildrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$grandChildrenCategoryCount[1] : 10;

        //get orderby
        preg_match('/grand-children-category-sort="(.*?)"/', $matches, $sortName);
        preg_match('/grand-children-category-sort-order="(.*?)"/', $matches, $sortOrder);
        $grandChildrenCategoriesData = ArticleCategory::where('parent_uuid', $childrenCategoryData->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($grandChildrenCategoryCount);

        return preg_replace_callback('/<grand-children-category-element.*?>(.*?)<\/grand-children-category-element>/s', function ($grandChildMatches) use ($grandChildrenCategoriesData) {
            $grandChildrenCategoryData = $grandChildrenCategoriesData->shift();
            $grandChildSearchReplaceMap = $this->searchReplaceMapForGrandChildrenCategory($grandChildrenCategoryData);
            return str_replace(array_keys($grandChildSearchReplaceMap), $grandChildSearchReplaceMap, $grandChildMatches[0]);
        }, $matches);
    }

    private function searchReplaceMapForChildrenCategory($childrenCategory = null)
    {
        if (!empty($childrenCategory)) {
            return [
                '{children_category.uuid}' => $childrenCategory->uuid ?? null,
                '{children_category.slug}' => $childrenCategory->slug ?? null,
                '{children_category.title}' => $childrenCategory->title ?? null,
                '{children_category.content}' => $childrenCategory->content ?? null,
                '{children_category.feature_image}' => $childrenCategory->feature_image ?? null,
                '{children_category.image}' => $childrenCategory->image ?? null,
                '{children_category.keyword}' => $childrenCategory->keyword ?? null,
                '{children_category.description}' => $childrenCategory->description ?? null,
                '{children_category.short_content}' => $childrenCategory->short_content ?? null,
            ];
        } else {

            return [
                '{children_category.uuid}' => 'This is children category sample',
                '{children_category.slug}' => 'This is children category sample',
                '{children_category.title}' => 'This is children category sample',
                '{children_category.content}' => 'This is children category sample',
                '{children_category.feature_image}' => 'This is children category sample',
                '{children_category.image}' => 'This is children category sample',
                '{children_category.keyword}' => 'This is children category sample',
                '{children_category.description}' => 'This is children category sample',
                '{children_category.short_content}' => 'This is children category sample',
            ];
        }
    }

    private function searchReplaceMapForGrandChildrenCategory($grandChildrenCategory = null)
    {
        if (!empty($grandChildrenCategory)) {
            return [
                '{grand_children_category.uuid}' => $grandChildrenCategory->uuid ?? null,
                '{grand_children_category.slug}' => $grandChildrenCategory->slug ?? null,
                '{grand_children_category.title}' => $grandChildrenCategory->title ?? null,
                '{grand_children_category.content}' => $grandChildrenCategory->content ?? null,
                '{grand_children_category.feature_image}' => $grandChildrenCategory->feature_image ?? null,
                '{grand_children_category.image}' => $grandChildrenCategory->image ?? null,
                '{grand_children_category.keyword}' => $grandChildrenCategory->keyword ?? null,
                '{grand_children_category.description}' => $grandChildrenCategory->description ?? null,
                '{grand_children_category.short_content}' => $grandChildrenCategory->short_content ?? null,
            ];
        } else {
            return [
                '{grand_children_category.uuid}' => 'This is grand children category sample',
                '{grand_children_category.slug}' => 'This is grand children category sample',
                '{grand_children_category.title}' => 'This is grand children category sample',
                '{grand_children_category.content}' => 'This is grand children category sample',
                '{grand_children_category.feature_image}' => 'This is grand children category sample',
                '{grand_children_category.image}' => 'This is grand children category sample',
                '{grand_children_category.keyword}' => 'This is grand children category sample',
                '{grand_children_category.description}' => 'This is grand children category sample',
                '{grand_children_category.short_content}' => 'This is grand children category sample',
            ];
        }
    }

    public function replaceChildrenCategoryJson($components, $articleCategory)
    {
        return $this->processComponents($components);
    }

    public function processComponents($components) {
        foreach ($components as $component) {
            if (isset($component->tagName) && $component->tagName == 'children-category-list') {
                $childrenCategoryCount = $component->attributes->{'data-children-category-count'} ?? 10;
                $sortName = $component->attributes->{'children-category-sort'} ?? 'created_at';
                $sortOrder = $component->attributes->{'children-category-sort-order'} ?? 'DESC';
                $component->components = $this->replaceChildrenCategoryListJson($component->components, $childrenCategoryCount, $sortName, $sortOrder);
            }
            if (isset($component->components)) {
                $this->processComponents($component->components);
            }
        }

        return $components;
    }


    public function replaceChildrenCategoryListJson($components, $childrenCategoryCount, $sortName, $sortOrder)
    {
        $childrenCategoriesData = ArticleCategory::orderBy($sortName, $sortOrder)->paginate($childrenCategoryCount);
        foreach ($components as $key => $childrenCategoryElement) {
            $childrenCategoryData = $childrenCategoriesData->shift();
            if (!$childrenCategoryData) {
                continue;
            }
            $childrenCategoryElement = $this->replaceGrandChildrenCategoryListJson($childrenCategoryElement, $childrenCategoryData);
            $childrenCategoryElementDecode = json_encode($childrenCategoryElement);
            $childSearchReplaceMap = $this->searchReplaceMapForChildrenCategory($childrenCategoryData);

            $components[$key] = json_decode(str_replace(array_keys($childSearchReplaceMap), $childSearchReplaceMap, $childrenCategoryElementDecode));
        }

        return $components;
    }


    public function replaceGrandChildrenCategoryListJson($grandChildrenCategoryElement, $childrenCategoryData)
    {
        $grandChildrenCategoriesData = ArticleCategory::where('parent_uuid', $childrenCategoryData->uuid)->paginate(10);
        $this->replaceInNestedComponents($grandChildrenCategoryElement->components, $grandChildrenCategoriesData);

        return $grandChildrenCategoryElement;
    }

    private function replaceInNestedComponents(&$components, $data)
    {
        foreach ($components as &$component) {
            if (isset($component->tagName) && $component->tagName === 'grand_children_category') {
                $categoryData = $data->shift();
                $this->replaceDataInComponent($component, $categoryData);
            }
            if (isset($component->components)) {
                $this->replaceInNestedComponents($component->components, $data);
            }
        }
    }

    private function replaceDataInComponent(&$component, $newData)
    {
        $searchReplaceMap = $this->searchReplaceMapForGrandChildrenCategory($newData);
        $component->components = json_decode(str_replace(array_keys($searchReplaceMap), $searchReplaceMap, json_encode($component->components)));
    }
}
