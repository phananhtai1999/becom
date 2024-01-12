<?php

namespace App\Services;

use Techup\ApiBase\Services\AppCallService;

class ShopService extends AppCallService
{

    public function getProductDetailData($productUuid) {

        return $this->callService('ecom', 'get', 'data-product-detail/' . $productUuid, '', auth()->appId(), auth()->userId());
    }

    public function getProductCategoryData($categorySlug) {
        $data = [
            'product_category_slug' => $categorySlug
        ];

        return $this->callService('ecom', 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getListProductByCategoryData($categorySlug) {
        $data = [
            'product_category_slug' => $categorySlug
        ];

        return $this->callService('ecom', 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getChildrenByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $data = [
            'product_category_uuid' => $categoryUuid,
            'per_page' => $childrenCategoryCount,
            'sorted_by' => $sortOrder,
            'sort' => $sortName
        ];

        return $this->callService('ecom', 'get', 'children-category', $data, auth()->appId(), auth()->userId());
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

        return $this->callService('ecom', 'get', 'products-by-category', $data, auth()->appId(), auth()->userId());
    }

    public function getProductByUuid($productUuid) {

        return $this->callService('ecom', 'get', 'product/' . $productUuid, '', auth()->appId(), auth()->userId());
    }

    public function getProductByParentCategoryUuid($categoryUuid) {
        return $this->callService('ecom', 'get', 'product-by-parent-category/' . $categoryUuid, '', auth()->appId(), auth()->userId());
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
