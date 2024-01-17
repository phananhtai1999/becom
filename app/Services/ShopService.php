<?php

namespace App\Services;

use Techup\ApiBase\Services\AppCallService;

class ShopService extends AppCallService
{

    public function getProductDetailData($productUuid) {

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-detail/' . $productUuid, '', auth()->appId(), auth()->userId());
    }

    public function getProductCategoryData($categorySlug) {
        $data = [
            'product_category_slug' => $categorySlug
        ];

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getListProductByCategoryData($categorySlug) {
        $data = [
            'product_category_slug' => $categorySlug
        ];

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getChildrenByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $data = [
            'product_category_uuid' => $categoryUuid,
            'per_page' => $childrenCategoryCount,
            'sorted_by' => $sortOrder,
            'sort' => $sortName
        ];

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'children-category', $data, auth()->appId(), auth()->userId());
    }

    public function getProductHeader($sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $data = [
            'per_page' => $childrenCategoryCount,
            'sorted_by' => $sortOrder,
            'sort' => $sortName
        ];

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'product-header', $data, auth()->appId(), auth()->userId());
    }

    public function getListProductByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        if (empty($categoryUuid)) {
            $data = [
                'per_page' => $childrenCategoryCount,
                'sorted_by' => $sortOrder,
                'sort' => $sortName
            ];
        } else {
            $data = [
                'product_category_uuid' => $categoryUuid,
                'per_page' => $childrenCategoryCount,
                'sorted_by' => $sortOrder,
                'sort' => $sortName
            ];
        }

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'products-by-category', $data, auth()->appId(), auth()->userId());
    }

    public function getProductByUuid($productUuid) {

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'product/' . $productUuid, '', auth()->appId(), auth()->userId());
    }

    public function getProductByParentCategoryUuid($categoryUuid) {
        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'product-by-parent-category/' . $categoryUuid, '', auth()->appId(), auth()->userId());
    }

    public function myProduct($request)
    {
        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', '/my/products', $request->all(), auth()->appId(), auth()->userId());
    }

    public function myCategory($request)
    {
        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', '/my/categories', $request->all(), auth()->appId(), auth()->userId());
    }

}
