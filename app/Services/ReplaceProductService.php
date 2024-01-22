<?php

namespace App\Services;

use App\Models\Article;

class ReplaceProductService extends ShopService
{
    public function replaceListProduct($template, $productCategory, $websitePage)
    {
        $pattern = '/<product-list.*?>(.*?)<\/product-list>/s';

        return preg_replace_callback($pattern, function ($matches) use ($websitePage, $productCategory) {
            $productCount = $this->searchProductCount($matches[0]);
            $sortName = $this->searchProductSort($matches[0]);
            $sortOrder = $this->searchProductSortOrder($matches[0]);
            $productsData = $this->getListProductByCategoryUuid($productCategory['uuid'], $sortName, $sortOrder, $productCount);
            $productsData = $productsData['data']['data'];
            $pattern = '/<product-element.*?>(.*?)<\/product-element>/s';
            return preg_replace_callback($pattern, function ($matchProduct) use ($productsData, $websitePage) {
                $productData = array_shift($productsData);
                if (!$productData) {
                    return $matchProduct[0];
                }
                $searchReplaceMap = $this->searchReplaceMapForproduct($productData);

                return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchProduct[0]);
            }, $matches[0]);
        }, $template);
    }

    public function replaceListProductForPageHome($template, $websitePage)
    {
        $pattern = '/<product-list.*?>(.*?)<\/product-list>/s';

        return preg_replace_callback($pattern, function ($matches) use ($websitePage) {
            $replaceProductCategoryService = new ReplaceProductCategoryService();
            $replaceBrandService = new ReplaceBrandService();
            $replaceDimensionService = new ReplaceDimensionService();

            $productCount = $this->searchProductCount($matches[0]);
            $sortName = $this->searchProductSort($matches[0]);
            $sortOrder = $this->searchProductSortOrder($matches[0]);
            $sortFilterByCategory = $this->searchFilterByCategory($matches[0]);
            $expand = ['product__categories', 'product__brand', 'product__dimension'];
            $productsData = $this->getListProductByCategoryUuid($sortFilterByCategory, $sortName, $sortOrder, $productCount, $expand);
            $productsData = $productsData['data'];
            $pattern = '/<product-element.*?>(.*?)<\/product-element>/s';

            return preg_replace_callback($pattern, function ($matchesProduct) use (
                $productsData,
                $websitePage,
                $replaceProductCategoryService,
                $replaceBrandService,
                $replaceDimensionService
            ) {
                $productData = array_shift($productsData);
                if (!$productData) {

                    return $matchesProduct[0];
                }
                $category = $productData['categories'];
                $brand = $productData['brand'];
                $dimension = $productData['dimension'];
                $matchesProduct[0] = $replaceProductCategoryService->replaceCategoryInProduct($matchesProduct[0], $category);
                if (!empty($brand)) {
                    $matchesProduct[0] = $replaceBrandService->replaceBrand($matchesProduct[0], $brand);
                }
                if (!empty($dimension)) {
                    $matchesProduct[0] = $replaceDimensionService->replaceDimension($matchesProduct[0], $dimension);
                }
                $searchReplaceMap = $this->searchReplaceMapForProduct($productData);
                return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matchesProduct[0]);
            }, $matches[0]);

        }, $template);
    }

    public function searchReplaceMapForProduct($product)
    {
        return [
            '{product.uuid}' => $product['uuid'] ?? null,
            '{product.name}' => array_values($product['names'])[0] ?? null,
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

    public function searchProductCount($template): int
    {
        preg_match('/data-product-count="(\d+)"/', $template, $categoryCount);

        return isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;
    }

    public function searchProductSort($template)
    {
        preg_match('/product-sort="(.*?)"/', $template, $sortName);

        return $sortName[1] ?? 'created_at';
    }

    public function searchProductSortOrder($template)
    {
        preg_match('/product-sort-order="(.*?)"/', $template, $sortOrder);

        return $sortOrder[1] ?? 'desc';
    }

    public function searchFilterByCategory($template)
    {
        preg_match('/data-filter-product-by-category="(.*?)"/', $template, $sortFilterByCategory);

        return $sortFilterByCategory[1] ?? null;
    }
}
