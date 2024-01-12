<?php

namespace App\Services;

use Techup\ApiBase\Services\AppCallService;

class ShopService extends AppCallService
{

    public function getProductDetailData($productUuid) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'data-product-detail/' . $productUuid);
        return json_decode($res->getBody()->getContents(), true);
    }

    public function getProductCategoryData($categorySlug) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'data-product-category', ['query' => ['product_category_slug' => $categorySlug]]);
        return json_decode($res->getBody()->getContents(), true);
    }

    public function getListProductByCategoryData($categorySlug) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'data-product-category', ['query' => ['product_category_slug' => $categorySlug]]);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function getChildrenByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'children-category', [
            'query' => [
                'product_category_uuid' => $categoryUuid,
                'per_page' => $childrenCategoryCount,
                'sorted_by' => $sortOrder,
                'sort' => $sortName
            ]
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function getListProductByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'products-by-category', ['query' => [
            'product_category_uuid' => 1,
            'per_page' => $childrenCategoryCount,
            'sorted_by' => $sortOrder,
            'sort' => $sortName
        ]]);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function getProductByUuid($productUuid) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'product/' . $productUuid);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function getProductByParentCategoryUuid($categoryUuid) {
        $client = $this->createRequest();
        $res = $client->get(config('shop.shop_url') . 'product-by-parent-category/' . $categoryUuid);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function myProduct($request)
    {
        return $this->callService('ecom', 'get', '/my/products', $request->all(), auth()->appId(), auth()->userId());
    }

    public function myCategory($request)
    {
        return $this->callService('ecom', 'get', '/my/categories', $request->all(), auth()->appId(), auth()->userId());
    }

}
