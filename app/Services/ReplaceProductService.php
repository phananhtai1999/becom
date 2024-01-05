<?php

namespace App\Services;

use App\Models\Article;

class ReplaceProductService extends ShopService
{
    public function replaceListProduct($template, $productCategory, $websitePage)
    {
        $pattern = '/data-product-count="(\d+)"/';
        preg_match_all($pattern, $template, $productCount);
        $productCount = !empty($productCount[1]) ? array_sum($productCount[1]) : 10;
        preg_match('/product-sort="(.*?)"/', $template, $sortName);
        preg_match('/product-sort-order="(.*?)"/', $template, $sortOrder);
        $productsData = $this->getListProductByCategoryUuid($productCategory['uuid'], $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $productCount ?? 10);
        $productsData = $productsData['data'];
        $pattern = '/<product.*?>(.*?)<\/product>/s';

        return preg_replace_callback($pattern, function ($matches) use ($productsData, $websitePage) {
            $productData = $productsData->shift();
            if (!$productData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForproduct($productData);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $template);
    }

    public function replaceListProductForPageHome($template, $websitePage) {

        //get number article need to parse
        $pattern = '/data-product-count="(\d+)"/';
        preg_match_all($pattern, $template, $matches);
        $numbers = array_map('intval', $matches[1]);
        $productCount = array_sum($numbers);

        //get orderby
        preg_match('/product-sort="(.*?)"/', $template, $sortName);
        preg_match('/product-sort-order="(.*?)"/', $template, $sortOrder);
        preg_match('/data-filter-product-by-category="(.*?)"/', $template, $sortFilterByCategory);
        if ($sortFilterByCategory) {
            $productsData = $this->getListProductByCategoryUuid($sortFilterByCategory, $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $productCount ?? 10);
        } else {
            $productsData = $this->getListProductByCategoryUuid(null, $sortName[1] ?? 'created_at', $sortOrder[1] ?? 'desc', $productCount ?? 10);
        }
        $productsData = $productsData['data']['data'];
        $pattern = '/<product-element.*?>(.*?)<\/product-element>/s';
        return preg_replace_callback($pattern, function ($matches) use ($productsData, $websitePage) {
            $productData = array_shift($productsData);

            if (!$productData) {
                return $matches[0];
            }

            $category = $productData['category'];
            $replaceProductCategoryService = new ReplaceProductCategoryService();
            $replaceProductCategoryService->replaceCategoryInProduct($matches[0], $category);

            $searchReplaceMap = $this->searchReplaceMapForProduct($productData);
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
            preg_match('/data-product-specific="(.*?)"/', $matches[0], $productUuid);
            $productData = $this->getProductByUuid($productUuid);
            $product = $productData['data'];
            if (!$product) {
                return $matches[0];
            }
            $searchReplaceMap = $this->searchReplaceMapForProduct($product);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $specificProductList);
    }
}
