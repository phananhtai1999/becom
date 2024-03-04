<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class ReplaceArticleService
{
    public function replaceListArticle($template, $articleCategory, $websitePage) {
        $pattern = '/<article-list.*?>(.*?)<\/article-list>/s';

        return preg_replace_callback($pattern, function ($matches) use ($websitePage, $articleCategory){
            $articleCount = $this->searchArticleCount($matches[0]);
            $sortName = $this->searchArticleSort($matches[0]);
            $sortOrder = $this->searchArticleSortOrder($matches[0]);
            $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName, $sortOrder)->paginate($articleCount);
            $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';

            return preg_replace_callback($pattern, function ($matchesArticle) use ($articlesData, $websitePage) {
                $articleData = $articlesData->shift();
                if (!$articleData) {
                    return $matchesArticle[0];
                }
//                $matchesArticle[0] = $this->replaceRedirectTag($articleData, $websitePage, $matchesArticle[0]);
                $searchReplaceMap = $this->searchReplaceMapForArticle($articleData);

                return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchesArticle[0]);
            }, $matches[0]);
        }, $template);
    }

    public function replaceListArticleSpecific($template, $websitePage) {
        preg_match('/<specific-article-list.*?>(.*?)<\/specific-article-list>/s', $template, $specificArticleList);
        if (!$specificArticleList) {
            return $template;
        }
        $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';
        $replaceCategoryService = new ReplaceCategoryService();
        return preg_replace_callback($pattern, function ($matches) use ($websitePage, $replaceCategoryService) {
            preg_match('/data-article-specific="(.*?)"/', $matches[0], $articleUuid);
            if (!$articleUuid) {
                return $matches[0];
            }
            $article = Article::where(['uuid' => $articleUuid[1]])->first();
            if (!$article) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForArticle($article);
            $category = $article->articleCategory;
            $matches[0] = $replaceCategoryService->replaceCategoryInArticle($matches[0], $category);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $specificArticleList[0]);
    }

    public function replaceListArticleForPageHome($template, $websitePage) {

        $pattern = '/<article-list.*?>(.*?)<\/article-list>/s';
        return preg_replace_callback($pattern, function ($matches) use ($websitePage){
            preg_match('/article-sort="(.*?)"/', $matches[0], $sortName);
            preg_match('/article-sort-order="(.*?)"/', $matches[0], $sortOrder);
            preg_match('/data-filter-article-by-category="(.*?)"/', $matches[0], $sortFilterByCategory);
            preg_match('/data-article-count="(\d+)"/', $matches[0], $articleCount);
            $articleCount = !empty($articleCount) ? (int)$articleCount[1] : 10;
            if ($sortFilterByCategory) {
                $articlesData = Article::where('article_category_uuid', $sortFilterByCategory[1])->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
            } else {
                $articlesData = Article::orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
            }
            $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';

            return preg_replace_callback($pattern, function ($matchesArticle) use ($articlesData, $websitePage) {
                $articlesData = $articlesData->shift();
                if (!$articlesData) {
                    return $matchesArticle[0];
                }

                $category = $articlesData->articleCategory;
                $replaceCategoryService = new ReplaceCategoryService();
                $matchesArticle[0] = $replaceCategoryService->replaceCategoryInArticle($matchesArticle[0], $category);
                $searchReplaceMap = $this->searchReplaceMapForArticle($articlesData);
                return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchesArticle[0]);
            }, $matches[0]);

        }, $template);
    }

    public function replaceRedirectTag($article, $websitePage, $template) {
        $domain = $websitePage->websites()->first()->domain;
        $category = $article->articleCategory;
        $replaceCategoryService = new ReplaceCategoryService();
        $template = $this->replaceDomain($domain, $template);

        return $replaceCategoryService->replaceCategoryInArticle($template, $category);
    }

    public function replaceDomain($domain, $template) {
        $searchReplaceMap = [
            '{domain.slug}' => $domain->slug ?? null,
        ];

        return Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $template);
    }


    public function searchReplaceMapForArticle($article = null)
    {
        if (!empty($article)) {
            if (request()->get('is_preview')) {
                $slug = $article->slug . '-' . $article->uuid;
            } else {
                $slug = $article->slug;
            }

            return [
                '{article.uuid}' => $article->uuid ?? null,
                '{article.article_category_uuid}' => $article->article_category_uuid ?? null,
                '{article.slug}' => $slug,
                '{article.title}' => $article->title ?? null,
                '{article.content}' => $article->content ?? null,
                '{article.video}' => $article->video ?? null,
                '{article.image}' => $article->image ?? null,
                '{article.keyword}' => $article->keyword ?? null,
                '{article.description}' => $article->description ?? null,
                '{article.short_content}' => $article->short_content ?? null,
            ];
        } else {
            return [
                '{article.uuid}' => 'This is article uuid sample',
                '{article.article_category_uuid}' => 'This is article article_category_uuid sample',
                '{article.slug}' => 'This is article slug sample',
                '{article.title}' => 'This is article title sample',
                '{article.content}' => 'This is article content sample',
                '{article.video}' => 'This is article video sample',
                '{article.image}' => 'This is article image sample',
                '{article.keyword}' => 'This is article keyword sample',
                '{article.description}' => 'This is article description sample',
                '{article.short_content}' => 'This is article short_content sample',
            ];
        }

    }

    public function searchArticleCount($template): int
    {
        preg_match('/data-article-count="(\d+)"/', $template, $categoryCount);

        return isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;
    }

    public function searchArticleSort($template)
    {
        preg_match('/article-sort="(.*?)"/', $template, $sortName);

        return $sortName[1] ?? 'created_at';
    }

    public function searchArticleSortOrder($template)
    {
        preg_match('/article-sort-order="(.*?)"/', $template, $sortOrder);

        return $sortOrder[1] ?? 'DESC';
    }

    public function replaceArticleJson($components, $articleCategory)
    {
        foreach ($components as $component) {
            if (isset($component->tagName) && $component->tagName == 'article-list') {
                $childrenCategoryCount = $component->attributes->{'data-category-count'} ?? 10;
                $sortName = $component->attributes->{'category-sort'} ?? 'created_at';
                $sortOrder = $component->attributes->{'category-sort-order'} ?? 'DESC';
                $component->components = $this->replaceArticleListJson($component->components, $childrenCategoryCount, $sortName, $sortOrder, $articleCategory);
            }

            if (isset($component->components)) {
                $this->replaceArticleJson($component->components, $articleCategory);
            }
        }
        return $components;
    }

    public function replaceArticleListJson($components, $childrenCategoryCount, $sortName, $sortOrder, $articleCategory = null)
    {
        if (!empty($articleCategory)) {
            $articlesDatas = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName, $sortOrder)->paginate($childrenCategoryCount);
        } else {
            $articlesDatas = Article::orderBy($sortName, $sortOrder)->paginate($childrenCategoryCount);
        }

        foreach ($components as $key => $component) {
            if (isset($component->tagName) && $component->tagName == 'article-element') {
                $articlesData = $articlesDatas->shift();

                $articleElementDecode = json_encode($component);
                $childSearchReplaceMap = $this->searchReplaceMapForArticle($articlesData);
                $components[$key] = json_decode(str_replace(array_keys($childSearchReplaceMap), $childSearchReplaceMap, $articleElementDecode));
            }
        }

        return $components;
    }

    public function replaceListArticleForPageHomeJson($components)
    {
        foreach ($components as $component) {
            if (isset($component->tagName) && $component->tagName == 'article-list') {
                $childrenCategoryCount = $component->attributes->{'data-article-count'} ?? 10;
                $sortName = $component->attributes->{'article-sort'} ?? 'created_at';
                $sortOrder = $component->attributes->{'article-sort-order'} ?? 'DESC';
                $component->components = $this->replaceArticleListJson($component->components, $childrenCategoryCount, $sortName, $sortOrder);
            }

            if (isset($component->components)) {
                $this->replaceListArticleForPageHomeJson($component->components);
            }
        }
        return $components;
    }

    public function replaceListArticleSpecificJson($components) {
        foreach ($components as $component) {
            if (isset($component->tagName) && $component->tagName == 'specific-article-list') {
                $component->components = $this->replaceSpecificListJson($component->components);
            }

            if (isset($component->components)) {
                $this->replaceListArticleSpecificJson($component->components);
            }
        }
        return $components;
    }

    private function replaceSpecificListJson($components)
    {
        foreach ($components as $key => $component) {
            if (isset($component->attributes->{'data-article-specific'})) {
                $article = Article::where(['uuid' => $component->attributes->{'data-article-specific'}])->first();
                if (!$article) {
                    continue;
                }
                $articleElementDecode = json_encode($component);
                $childSearchReplaceMap = $this->searchReplaceMapForArticle($article);
                $components[$key] = json_decode(str_replace(array_keys($childSearchReplaceMap), $childSearchReplaceMap, $articleElementDecode));
            }
        }

        return $components;
    }
}
