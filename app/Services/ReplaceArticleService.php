<?php

namespace App\Services;

use App\Models\Article;

class ReplaceArticleService
{
    public function replaceListArticle($template, $articleCategory) {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $template, $articleCount);
        $articleCount = isset($articleCount[1]) ? array_sum($articleCount[1]) : 10;
        preg_match('/article-sort="(.*?)"/', $template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $template, $sortOrder);
        $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        return preg_replace_callback($pattern, function ($matches) use ($articlesData) {
            $articleData = $articlesData->shift();
            if (!$articleData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForArticle($articleData);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $template);
    }

    public function replaceListArticleSpecific($template) {
        preg_match('/<specific_article_list.*?>(.*?)<\/specific_article_list>/s', $template, $specificArticleList);
        if (!$specificArticleList) {
            return $template;
        }
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        return preg_replace_callback($pattern, function ($matches) {
            preg_match('/data-article-specific="(.*?)"/', $matches[0], $articleUuid);
            $article = Article::find($articleUuid);
            if (!$article) {
                return $matches[0];
            }
            $searchReplaceMap = $this->searchReplaceMapForArticle($article);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $specificArticleList);
    }

    public function replaceListArticleForPageHome($template) {

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
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        return preg_replace_callback($pattern, function ($matches) use ($articlesData) {
            $articlesData = $articlesData->shift();
            if (!$articlesData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForArticle($articlesData);
            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $template);
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
