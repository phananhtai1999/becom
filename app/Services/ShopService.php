<?php

namespace App\Services;

use Techup\ApiBase\Services\AppCallService;

class ShopService extends AppCallService
{

    public function getProductDetailData($productUuid, $productSlug = null) {
        $expand = ['product__categories', 'product__brand'];
        $data = [
            'product_slug' => $productSlug
        ];
        if(empty($productSlug)) {
            $data = [
                'product_uuid' => $productUuid
            ];
        }
        $data = array_merge($data, ['expand' => $expand]);
        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-detail', $data, auth()->appId(), auth()->userId());
    }

    public function getProductCategoryData($categorySlug, $categoryUuid = null) {
        $data = [
            'product_category_slug' => $categorySlug
        ];
        if(empty($categorySlug)) {
            $data = [
                'product_category_uuid' => $categoryUuid
            ];
        }

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getListProductByCategoryData($categorySlug) {
        $data = [
            'product_category_slug' => $categorySlug
        ];

        return $this->callService(env('SHOP_SERVICE_NAME'), 'get', 'data-product-category', $data, auth()->appId(), auth()->userId());
    }

    public function getChildrenByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10) {
        $sort = $this->getSort($sortOrder, $sortName);
        $data = [
            'product_category_uuid' => $categoryUuid,
            'per_page' => $childrenCategoryCount,
            'sort' => $sort
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

    public function getListProductByCategoryUuid($categoryUuid, $sortName = 'created_at', $sortOrder = 'desc', $childrenCategoryCount = 10, $expand = []) {
        if (empty($categoryUuid)) {
            $data = [
                'per_page' => $childrenCategoryCount,
                'sorted_by' => $sortOrder,
                'sort' => $sortName,
                'expand' => $expand
            ];
        } else {
            $data = [
                'product_category_uuid' => $categoryUuid,
                'per_page' => $childrenCategoryCount,
                'sorted_by' => $sortOrder,
                'sort' => $sortName,
                'expand' => $expand
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

    public function getSort($sortOrder, $sortName): string
    {
        if ($sortOrder == 'desc') {
            $sort = '-' . $sortName;
        } else {
            $sort = $sortName;
        }
        return $sort;
    }

}
