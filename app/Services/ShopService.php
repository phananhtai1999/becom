<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class ShopService extends AbstractService
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
        $res = $client->get(config('shop.shop_url') . 'children-category/'. $categoryUuid, [
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
            'product_category_uuid' => $categoryUuid,
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

}
