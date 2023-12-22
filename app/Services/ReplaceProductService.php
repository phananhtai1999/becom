<?php

namespace App\Services;

use App\Models\Article;

class ReplaceProductService
{
    public function replaceListArticle($template, $articleCategory, $websitePage)
    {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $template, $articleCount);
        $articleCount = isset($articleCount[1]) ? array_sum($articleCount[1]) : 10;
        preg_match('/article-sort="(.*?)"/', $template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $template, $sortOrder);
        $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';

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

    public function searchReplaceMapForProduct($product)
    {
        return [
            '{product.uuid}' => $product['uuid'] ?? null,
            '{product.name}' => array_values($product['name'])[0] ?? null,
            '{product.brand_uuid}' => $product['brand_uuid'] ?? null,
            '{product.product_dimension_uuid}' => $product['product_dimension_uuid'] ?? null,
            '{product.slug}' => $product['slug'] ?? null,
            '{product.condition}' => $product['condition'] ?? null,
            '{product.description}' => $product['description'] ?? null,
            '{product.price}' => $product['price'] ?? null,
            '{product.price_before_discount}' => $product['price_before_discount'] ?? null,
            '{product.image}' => $product['image'] ?? null,
            '{product.stock}' => $product['stock'] ?? null,
            '{product.availability}' => $product['availability'] ?? null,
            '{product.availability_date}' => $product['availability_date'] ?? null,
            '{product.gtin}' => $product['gtin'] ?? null,
            '{product.mpn}' => $product['mpn'] ?? null,
        ];

    }

    public function replaceListProductSpecific(string $template, $websitePage)
    {
        preg_match('/<specific_product_list.*?>(.*?)<\/specific_product_list>/s', $template, $specificProductList);
        if (!$specificProductList) {
            return $template;
        }
        $pattern = '/<product.*?>(.*?)<\/product>/s';

        return preg_replace_callback($pattern, function ($matches) use ($websitePage) {
            preg_match('/data-product-specific="(.*?)"/', $matches[0], $articleUuid);
            $article = Article::find($articleUuid);
            if (!$article) {
                return $matches[0];
            }
            $searchReplaceMap = $this->searchReplaceMapForProduct($article);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $specificProductList);
    }
}
