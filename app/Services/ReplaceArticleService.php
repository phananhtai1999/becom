<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class ReplaceArticleService
{
    public function replaceListArticle($template, $articleCategory, $websitePage) {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $template, $articleCount);
        $articleCount = isset($articleCount[1]) ? array_sum($articleCount[1]) : 10;
        preg_match('/article-sort="(.*?)"/', $template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $template, $sortOrder);
        $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';

        return preg_replace_callback($pattern, function ($matches) use ($articlesData, $websitePage) {
            $articleData = $articlesData->shift();
            if (!$articleData) {
                return $matches[0];
            }

            $matches[0] = $this->replaceRedirectTag($articleData, $websitePage, $matches[0]);

            $searchReplaceMap = $this->searchReplaceMapForArticle($articleData);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $template);
    }

    public function replaceListArticleSpecific($template, $websitePage) {
        preg_match('/<specific-article-list.*?>(.*?)<\/specific-article-list>/s', $template, $specificArticleList);
        if (!$specificArticleList) {
            return $template;
        }
        $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';

        return preg_replace_callback($pattern, function ($matches) use ($websitePage) {
            preg_match('/data-article-specific="(.*?)"/', $matches[0], $articleUuid);
            $article = Article::find($articleUuid);
            if (!$article) {
                return $matches[0];
            }
            $searchReplaceMap = $this->searchReplaceMapForArticle($article);

            $matches[0] = $this->replaceRedirectTag($article, $websitePage, $matches[0]);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $specificArticleList);
    }

    public function replaceListArticleForPageHome($template, $websitePage) {

        //get number article need to parse
        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $template, $matches);
        $numbers = array_map('intval', $matches[1]);
        $articleCount = array_sum($numbers);
        $articleCount = isset($articleCount) ? (int)$articleCount : 10;

        //get orderby
        preg_match('/article-sort="(.*?)"/', $template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $template, $sortOrder);
        preg_match('/data-filter-article-by-category="(.*?)"/', $template, $sortFilterByCategory);
        if ($sortFilterByCategory) {
            $articlesData = Article::where('article_category_uuid', $sortFilterByCategory[1])->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        } else {
            $articlesData = Article::orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        }
        $pattern = '/<article-element.*?>(.*?)<\/article-element>/s';
        return preg_replace_callback($pattern, function ($matches) use ($articlesData, $websitePage) {
            $articlesData = $articlesData->shift();
            if (!$articlesData) {
                return $matches[0];
            }
            //replace slug
            $matches[0] = $this->replaceRedirectTag($articlesData, $websitePage, $matches[0]);
            $category = $articlesData->articleCategory;
            $replaceCategoryService = new ReplaceCategoryService();
            $replaceCategoryService->replaceCategoryInArticle($matches[0], $category);

            $searchReplaceMap = $this->searchReplaceMapForArticle($articlesData);
            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
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
            return [
                '{article.uuid}' => $article->uuid ?? null,
                '{article.article_category_uuid}' => $article->article_category_uuid ?? null,
                '{article.slug}' => $article->slug ?? null,
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

}
