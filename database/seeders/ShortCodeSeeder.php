<?php

namespace Database\Seeders;

use App\Models\ShortCode;
use App\Models\ShortCodeGroup;
use App\Models\WebsitePageShortCode;
use Illuminate\Database\Seeder;

class ShortCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parentShortCodes = [
            [
                'name' => 'article list',
                'key' => 'article_list',
                'short_code' => 'article_list',
                'short_code_groups' => [ShortCodeGroup::HOME_ARTICLES]
            ],
            [
                'name' => 'specific article list',
                'key' => 'specific_article_list',
                'short_code' => 'specific_article_list',
                'short_code_groups' => [ShortCodeGroup::ARTICLE_CATEGORY, ShortCodeGroup::ARTICLE_DETAIL]
            ],
            [
                'name' => 'category list',
                'key' => 'category_list',
                'short_code' => 'category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_ARTICLES]
            ],
            [
                'name' => 'children category list',
                'key' => 'children_category_list',
                'short_code' => 'children_category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_ARTICLES]
            ],
            [
                'name' => 'grand children category',
                'key' => 'grand_children_category_list',
                'short_code' => 'grand_children_category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_ARTICLES]
            ],
            [
                'name' => 'product list',
                'key' => 'product_list',
                'short_code' => 'product_list',
                'short_code_groups' => [ShortCodeGroup::HOME_PRODUCTS]
            ],
            [
                'name' => 'specific product list',
                'key' => 'specific_product_list',
                'short_code' => 'specific_product_list',
                'short_code_groups' => [ShortCodeGroup::PRODUCT_CATEGORY, ShortCodeGroup::PRODUCT_DETAIL]
            ],
            [
                'name' => 'product category list',
                'key' => 'product_category_list',
                'short_code' => 'product_category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_PRODUCTS]
            ],
            [
                'name' => 'children product category list',
                'key' => 'children_product_category_list',
                'short_code' => 'children_product_category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_PRODUCTS]
            ],
            [
                'name' => 'grand children product category',
                'key' => 'grand_children_product_category_list',
                'short_code' => 'grand_children_product_category_list',
                'short_code_groups' => [ShortCodeGroup::HOME_PRODUCTS]
            ],
            [
                'name' => 'count',
                'key' => 'count',
                'short_code' => 'count_element',
                'short_code_groups' => [
                    ShortCodeGroup::ARTICLE_CATEGORY, ShortCodeGroup::HOME_ARTICLES, ShortCodeGroup::ARTICLE_DETAIL,
                    ShortCodeGroup::PRODUCT_CATEGORY, ShortCodeGroup::HOME_PRODUCTS, ShortCodeGroup::PRODUCT_DETAIL,
                ]
            ],
        ];
        $elements = [
            'article' => [
                'name' => 'article element',
                'key' => 'article',
                'short_code' => 'article_element',
                'short_code_groups' => [ShortCodeGroup::ARTICLE_DETAIL]
            ],
            'category' => [
                'name' => 'category element',
                'key' => 'category',
                'short_code' => 'category_element',
                'short_code_groups' => [ShortCodeGroup::ARTICLE_CATEGORY]
            ],
            'children_category' => [
                'name' => 'children category',
                'key' => 'children_category',
                'short_code' => 'children_category_element',
            ],
            'grand_children_category' => [
                'name' => 'grand children category',
                'key' => 'grand_children_category',
                'short_code' => 'grand_children_category_element',
            ],
            'product' => [
                'name' => 'product element',
                'key' => 'product',
                'short_code' => 'product_element',
                'short_code_groups' => [ShortCodeGroup::PRODUCT_DETAIL]
            ],
            'product_category' => [
                'name' => 'product category element',
                'key' => 'product_category_element',
                'short_code' => 'product_category_element',
                'short_code_groups' => [ShortCodeGroup::PRODUCT_CATEGORY]
            ],
            'children_product_category' => [
                'name' => 'children product category',
                'key' => 'children_product_category_element',
                'short_code' => 'children_product_category_element',
            ],
            'grand_children_product_category' => [
                'name' => 'grand children product category',
                'key' => 'grand_children_product_category_element',
                'short_code' => 'grand_children_product_category_element',
            ],
            'dimension' => [
                'name' => 'dimension of product',
                'key' => 'dimension_element',
                'short_code' => 'dimension_element',
            ],
            'brand' => [
                'name' => 'brand of product',
                'key' => 'brand_element',
                'short_code' => 'brand_element',
            ],
        ];
        $filters = [
            'article_filter' => [
                'name' => 'filter article',
                'key' => 'filter_article',
                'short_code' => 'data-filter-article-by-category',
            ],
            'product_filter' => [
                'name' => 'filter product',
                'key' => 'filter_product',
                'short_code' => 'data-filter-product-by-category',
            ],
        ];

        $specifics = [
            'article' => [
                'name' => 'data article specific',
                'key' => 'data_article_specific',
                'short_code' => 'data-article-specific',
            ],
            'product' => [
                'name' => 'data product specific',
                'key' => 'data_product_specific',
                'short_code' => 'data-product-specific',
            ],
        ];
        $sorts = [
            'article_sort' =>
                [
                    'name' => 'sort for article',
                    'key' => 'article_sort',
                    'short_code' => 'article-sort',
                ],
            'category_sort' =>
                [
                    'name' => 'sort for category',
                    'key' => 'category_sort',
                    'short_code' => 'category-sort',
                ],
            'children_category_sort' => [
                'name' => 'sort for children category',
                'key' => 'children_category_sort',
                'short_code' => 'children-category-sort',
            ],
            'grand_children_category_sort' => [
                'name' => 'sort for grand children category',
                'key' => 'grand_children_category_sort',
                'short_code' => 'grand-children-category-sort',
            ],
            'product_sort' =>
                [
                    'name' => 'sort for product',
                    'key' => 'product_sort',
                    'short_code' => 'product-sort',
                ],
            'product_category_sort' =>
                [
                    'name' => 'sort for product category',
                    'key' => 'product_category_sort',
                    'short_code' => 'product-category-sort',
                ],
            'children_product_category_sort' => [
                'name' => 'sort for children product category',
                'key' => 'children_category_sort',
                'short_code' => 'children-product-category-sort',
            ],
            'grand_children_product_category_sort' => [
                'name' => 'sort for grand children product category',
                'key' => 'grand_children_product_category_sort',
                'short_code' => 'grand-children-product-category-sort',
            ],
        ];
        $sortOrders = [
            'article_sort_order' => [
                'name' => 'sort order for article',
                'key' => 'article_sort_order',
                'short_code' => 'article-sort-order',
            ],
            'category_sort_order' => [
                'name' => 'sort order for category',
                'key' => 'category_sort_order',
                'short_code' => 'category-sort-order',
            ],
            'children_category_sort_order' => [
                'name' => 'sort order for children category',
                'key' => 'children_category_sort_order',
                'short_code' => 'children-category-sort-order',
            ],
            'grand_children_category_sort_order' => [
                'name' => 'sort order for grand children category',
                'key' => 'grand_children_category_sort_order',
                'short_code' => 'grand-children-category-sort-order',
            ],
            'product_sort_order' => [
                'name' => 'sort order for product',
                'key' => 'product_sort_order',
                'short_code' => 'product-sort-order',
            ],
            'product_category_sort_order' => [
                'name' => 'sort order for product category',
                'key' => 'product_category_sort_order',
                'short_code' => 'product-category-sort-order',
            ],
            'children_product_category_sort_order' => [
                'name' => 'sort order for product children category',
                'key' => 'children_product_category_sort_order',
                'short_code' => 'children-product-category-sort-order',
            ],
            'grand_children_product_category_sort_order' => [
                'name' => 'sort order for grand children product category',
                'key' => 'grand_children_product_category_sort_order',
                'short_code' => 'grand-children-product-category-sort-order',
            ],
        ];
        $counts = [
            'article_count' => [
                'name' => 'count for article',
                'key' => 'article_count',
                'short_code' => 'data-article-count',
            ],
            'category_count' => [
                'name' => 'count for category',
                'key' => 'category_count',
                'short_code' => 'data-category-count',
            ],
            'children_category_count' => [
                'name' => 'count for children category',
                'key' => 'children_category_count',
                'short_code' => 'data-children-category-count',
            ],
            'grand_children_category_count' => [
                'name' => 'count for grand children category ',
                'key' => 'grand_children_category_count',
                'short_code' => 'data-grand-children-category-count',
            ],
            'product_count' => [
                'name' => 'count for product',
                'key' => 'product_count',
                'short_code' => 'data-product-count',
            ],
            'product_category_count' => [
                'name' => 'count for product category',
                'key' => 'product_category_count',
                'short_code' => 'data-product-category-count',
            ],
            'children_product_category_count' => [
                'name' => 'count for children product category',
                'key' => 'children_product_category_count',
                'short_code' => 'data-children-product-category-count',
            ],
            'grand_children_product_category_count' => [
                'name' => 'count for grand children product category ',
                'key' => 'grand_children_product_category_count',
                'short_code' => 'data-grand-children-product-category-count',
            ],
        ];
        $shortCodes = [
            'article' => [
                [
                    'name' => 'uuid',
                    'key' => 'article.uuid',
                    'short_code' => 'article.uuid',
                ],
                [
                    'name' => 'slug',
                    'key' => 'article.slug',
                    'short_code' => 'article.slug',
                ],
                [
                    'name' => 'article category uuid',
                    'key' => 'article.article_category_uuid',
                    'short_code' => 'article.article_category_uuid',
                ],
                [
                    'name' => 'title',
                    'key' => 'article.title',
                    'short_code' => 'article.title',
                ],
                [
                    'name' => 'content',
                    'key' => 'article.content',
                    'short_code' => 'article.content',
                ],
                [
                    'name' => 'image',
                    'key' => 'article.image',
                    'short_code' => 'article.image',
                ],
                [
                    'name' => 'video',
                    'key' => 'article.video',
                    'short_code' => 'article.video',
                ],
                [
                    'name' => 'keyword',
                    'key' => 'article.keyword',
                    'short_code' => 'article.keyword',
                ],
                [
                    'name' => 'description',
                    'key' => 'article.description',
                    'short_code' => 'article.description',
                ],
                [
                    'name' => 'short content',
                    'key' => 'article.short_content',
                    'short_code' => 'article.short_content',
                ],
            ],
            'category' => [
                [
                    'name' => 'uuid',
                    'key' => 'category.uuid',
                    'short_code' => 'category.uuid',
                ],
                [
                    'name' => 'slug',
                    'key' => 'category.slug',
                    'short_code' => 'category.slug',
                ],
                [
                    'name' => 'title',
                    'key' => 'category.title',
                    'short_code' => 'category.title',
                ],
                [
                    'name' => 'content',
                    'key' => 'category.content',
                    'short_code' => 'category.content',
                ],
                [
                    'name' => 'image',
                    'key' => 'category.image',
                    'short_code' => 'category.image',
                ],
                [
                    'name' => 'feature image',
                    'key' => 'category.feature_image',
                    'short_code' => 'category.feature_image',
                ],
                [
                    'name' => 'keyword',
                    'key' => 'category.keyword',
                    'short_code' => 'category.keyword',
                ],
                [
                    'name' => 'description',
                    'key' => 'category.description',
                    'short_code' => 'category.description',
                ],
                [
                    'name' => 'short content',
                    'key' => 'category.short_content',
                    'short_code' => 'category.short_content',
                ],
            ],
            'children_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'children_category.uuid',
                    'short_code' => 'children_category.uuid',
                ],
                [
                    'name' => 'slug',
                    'key' => 'children_category.slug',
                    'short_code' => 'children_category.slug',
                ],
                [
                    'name' => 'title',
                    'key' => 'children_category.title',
                    'short_code' => 'children_category.title',
                ],
                [
                    'name' => 'content',
                    'key' => 'children_category.content',
                    'short_code' => 'children_category.content',
                ],
                [
                    'name' => 'image',
                    'key' => 'children_category.image',
                    'short_code' => 'children_category.image',
                ],
                [
                    'name' => 'feature image',
                    'key' => 'children_category.feature_image',
                    'short_code' => 'children_category.feature_image',
                ],
                [
                    'name' => 'keyword',
                    'key' => 'children_category.keyword',
                    'short_code' => 'children_category.keyword',
                ],
                [
                    'name' => 'description',
                    'key' => 'children_category.description',
                    'short_code' => 'children_category.description',
                ],
                [
                    'name' => 'short content',
                    'key' => 'children_category.short_content',
                    'short_code' => 'children_category.short_content',
                ],
            ],
            'grand_children_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'grand_children_category.uuid',
                    'short_code' => 'grand_children_category.uuid',
                ],
                [
                    'name' => 'slug',
                    'key' => 'grand_children_category.slug',
                    'short_code' => 'children_category.slug',
                ],
                [
                    'name' => 'title',
                    'key' => 'grand_children_category.title',
                    'short_code' => 'grand_children_category.title',
                ],
                [
                    'name' => 'content',
                    'key' => 'grand_children_category.content',
                    'short_code' => 'grand_children_category.content',
                ],
                [
                    'name' => 'image',
                    'key' => 'grand_children_category.image',
                    'short_code' => 'grand_children_category.image',
                ],
                [
                    'name' => 'feature image',
                    'key' => 'grand_children_category.feature_image',
                    'short_code' => 'grand_children_category.feature_image',
                ],
                [
                    'name' => 'keyword',
                    'key' => 'grand_children_category.keyword',
                    'short_code' => 'grand_children_category.keyword',
                ],
                [
                    'name' => 'description',
                    'key' => 'grand_children_category.description',
                    'short_code' => 'grand_children_category.description',
                ],
                [
                    'name' => 'short content',
                    'key' => 'grand_children_category.short_content',
                    'short_code' => 'grand_children_category.short_content',
                ],
            ],
            'article-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'article_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'article_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'article_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by content',
                    'key' => 'article_sort.content',
                    'short_code' => 'content',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'article_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by video',
                    'key' => 'article_sort.video',
                    'short_code' => 'video',
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'article_sort.keyword',
                    'short_code' => 'keyword',
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'article_sort.description',
                    'short_code' => 'description',
                ],
                [
                    'name' => 'sort by short content',
                    'key' => 'article_sort.short_content',
                    'short_code' => 'short_content',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'article_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'category_sort.feature_image',
                    'short_code' => 'feature_image',
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'category_sort.keyword',
                    'short_code' => 'keyword',
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'category_sort.description',
                    'short_code' => 'description',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'children-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'children_category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'children_category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'children_category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'children_category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'children_category_sort.feature_image',
                    'short_code' => 'feature_image',
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'children_category_sort.keyword',
                    'short_code' => 'keyword',
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'children_category_sort.description',
                    'short_code' => 'description',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'children_category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'grand-children-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'grand_children_category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'grand_children_category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'grand_children_category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'grand_children_category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'grand_children_category_sort.feature_image',
                    'short_code' => 'feature_image',
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'grand_children_category_sort.keyword',
                    'short_code' => 'keyword',
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'grand_children_category_sort.description',
                    'short_code' => 'description',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'grand_children_category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'article-sort-order' => [
                [
                    'name' => 'sort order by desc for article',
                    'key' => 'article_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for article',
                    'key' => 'article_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'category-sort-order' => [
                [
                    'name' => 'sort order by desc for category',
                    'key' => 'category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for category',
                    'key' => 'category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'children-category-sort-order' => [
                [
                    'name' => 'sort order by desc for children category',
                    'key' => 'children_category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'children_category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'grand-children-category-sort-order' => [
                [
                    'name' => 'sort order by desc for grand children category',
                    'key' => 'grand_children_category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'grand_children_category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],

            //Product here
            'product' => [
                [
                    'name' => 'uuid',
                    'key' => 'product.uuid',
                    'short_code' => 'product.uuid',
                ],                [
                    'name' => 'name',
                    'key' => 'product.name',
                    'short_code' => 'product.name',
                ],
                [
                    'name' => 'user uuid',
                    'key' => 'product.user_uuid',
                    'short_code' => 'product.user_uuid',
                ],
                [
                    'name' => 'app id',
                    'key' => 'product.app_id',
                    'short_code' => 'product.app_id',
                ],
                [
                    'name' => 'brand uuid',
                    'key' => 'product.brand_uuid',
                    'short_code' => 'product.brand_uuid',
                ],
                [
                    'name' => 'product dimension uuid',
                    'key' => 'product.product_dimension_uuid',
                    'short_code' => 'product.product_dimension_uuid',
                ],
                [
                    'name' => 'slug',
                    'key' => 'product.slug',
                    'short_code' => 'product.slug',
                ],
                [
                    'name' => 'condition',
                    'key' => 'product.condition',
                    'short_code' => 'product.condition',
                ],
                [
                    'name' => 'description',
                    'key' => 'product.description',
                    'short_code' => 'product.description',
                ],
                [
                    'name' => 'price',
                    'key' => 'product.price',
                    'short_code' => 'product.price',
                ],
                [
                    'name' => 'price before discount',
                    'key' => 'product.price_before_discount',
                    'short_code' => 'product.price_before_discount',
                ],
                [
                    'name' => 'image',
                    'key' => 'product.image',
                    'short_code' => 'product.image',
                ],
                [
                    'name' => 'images',
                    'key' => 'product.images',
                    'short_code' => 'product.images',
                ],
                [
                    'name' => 'stock',
                    'key' => 'product.stock',
                    'short_code' => 'product.stock',
                ],
                [
                    'name' => 'availability',
                    'key' => 'product.availability',
                    'short_code' => 'product.availability',
                ],
                [
                    'name' => 'availability date',
                    'key' => 'product.availability_date',
                    'short_code' => 'product.availability_date',
                ],
                [
                    'name' => 'expiration date',
                    'key' => 'product.expiration_date',
                    'short_code' => 'product.expiration_date',
                ],
                [
                    'name' => 'gtin',
                    'key' => 'product.gtin',
                    'short_code' => 'product.gtin',
                ],
                [
                    'name' => 'mpn',
                    'key' => 'product.mpn',
                    'short_code' => 'product.mpn',
                ],
            ],
            'product_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'product_category.uuid',
                    'short_code' => 'product_category.image',
                ],
                [
                    'name' => 'image',
                    'key' => 'product_category.image',
                    'short_code' => 'product_category.image',
                ],
                [
                    'name' => 'slug',
                    'key' => 'product_category.slug',
                    'short_code' => 'product_category.slug',
                ],
                [
                    'name' => 'parent uuid',
                    'key' => 'product_category.parent_uuid',
                    'short_code' => 'product_category.parent_uuid',
                ],
                [
                    'name' => 'title',
                    'key' => 'product_category.title',
                    'short_code' => 'product_category.title',
                ],
            ],
            'children_product_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'children_product_category.uuid',
                    'short_code' => 'children_product_category.uuid',
                ],
                [
                    'name' => 'image',
                    'key' => 'children_product_category.image',
                    'short_code' => 'children_product_category.image',
                ],
                [
                    'name' => 'slug',
                    'key' => 'children_product_category.slug',
                    'short_code' => 'children_product_category.slug',
                ],
                [
                    'name' => 'parent uuid',
                    'key' => 'children_product_category.parent_uuid',
                    'short_code' => 'children_product_category.parent_uuid',
                ],
                [
                    'name' => 'title',
                    'key' => 'children_product_category.title',
                    'short_code' => 'children_product_category.title',
                ],
            ],
            'grand_children_product_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'grand_children_product_category.uuid',
                    'short_code' => 'grand_children_product_category.uuid',
                ],
                [
                    'name' => 'image',
                    'key' => 'grand_children_product_category.image',
                    'short_code' => 'grand_children_product_category.image',
                ],
                [
                    'name' => 'slug',
                    'key' => 'grand_children_product_category.slug',
                    'short_code' => 'grand_children_product_category.slug',
                ],
                [
                    'name' => 'parent uuid',
                    'key' => 'grand_children_product_category.parent_uuid',
                    'short_code' => 'grand_children_product_category.parent_uuid',
                ],
                [
                    'name' => 'title',
                    'key' => 'grand_children_product_category.title',
                    'short_code' => 'grand_children_product_category.title',
                ],
            ],
            'product-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'product_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'name',
                    'key' => 'product_sort.name',
                    'short_code' => 'product_sort.name',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'product_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by parent uuid',
                    'key' => 'product_sort.parent_uuid',
                    'short_code' => 'parent_uuid',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'product_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by brand uuid',
                    'key' => 'product_sort.brand_uuid',
                    'short_code' => 'brand_uuid',
                ],
                [
                    'name' => 'sort by product dimension uuid',
                    'key' => 'product_sort.product_dimension_uuid',
                    'short_code' => 'product_dimension_uuid',
                ],
                [
                    'name' => 'sort by condition',
                    'key' => 'product_sort.condition',
                    'short_code' => 'condition',
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'product_sort.description',
                    'short_code' => 'description',
                ],
                [
                    'name' => 'sort by price',
                    'key' => 'product_sort.price',
                    'short_code' => 'price',
                ],
                [
                    'name' => 'sort by price before discount',
                    'key' => 'product_sort.price_before_discount',
                    'short_code' => 'price_before_discount',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'product_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by images',
                    'key' => 'product_sort.images',
                    'short_code' => 'images',
                ],
                [
                    'name' => 'sort by stock',
                    'key' => 'product_sort.stock',
                    'short_code' => 'stock',
                ],
                [
                    'name' => 'sort by availability',
                    'key' => 'product_sort.availability',
                    'short_code' => 'availability',
                ],
                [
                    'name' => 'sort by availability date',
                    'key' => 'product_sort.availability_date',
                    'short_code' => 'availability_date',
                ],
                [
                    'name' => 'sort by expiration date',
                    'key' => 'product_sort.expiration_date',
                    'short_code' => 'expiration_date',
                ],
                [
                    'name' => 'sort by gtin',
                    'key' => 'product_sort.gtin',
                    'short_code' => 'gtin',
                ],
                [
                    'name' => 'sort by mpn',
                    'key' => 'product_sort.mpn',
                    'short_code' => 'mpn',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'product_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'product-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'product_category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'product_category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by parent_uuid',
                    'key' => 'product_category_sort.parent_uuid',
                    'short_code' => 'parent_uuid',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'product_category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'product_category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'product_category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'children-product=category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'children_product_category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'children_product_category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'children_product_category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'children_product_category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by parent uuid',
                    'key' => 'children_product_category_sort.parent_uuid',
                    'short_code' => 'parent_uuid',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'children_product_category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'grand-children-product-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'grand_children_product_category_sort.uuid',
                    'short_code' => 'uuid',
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'grand_children_product_category_sort.slug',
                    'short_code' => 'slug',
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'grand_children_product_category_sort.title',
                    'short_code' => 'title',
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'grand_children_product_category_sort.image',
                    'short_code' => 'image',
                ],
                [
                    'name' => 'sort by parent uuid',
                    'key' => 'grand_children_product_category_sort.parent_uuid',
                    'short_code' => 'parent_uuid',
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'grand_children_product_category_sort.created_at',
                    'short_code' => 'created_at',
                ],
            ],
            'product-sort-order' => [
                [
                    'name' => 'sort order by desc for product',
                    'key' => 'product_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for product',
                    'key' => 'product_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'product-category-sort-order' => [
                [
                    'name' => 'sort order by desc for category',
                    'key' => 'product_category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for category',
                    'key' => 'product_category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'children-product-category-sort-order' => [
                [
                    'name' => 'sort order by desc for children category',
                    'key' => 'children_product_category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'children_product_category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'grand-children-product-category-sort-order' => [
                [
                    'name' => 'sort order by desc for grand children category',
                    'key' => 'grand_children_product_category_sort_order.desc',
                    'short_code' => 'desc',
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'grand_children_product_category_sort_order.asc',
                    'short_code' => 'asc',
                ]
            ],
            'brand' => [
                [
                    'name' => 'uuid',
                    'key' => 'product.brand.uuid',
                    'short_code' => 'product.brand.uuid',
                ],                [
                    'name' => 'name',
                    'key' => 'product.brand.name',
                    'short_code' => 'product.brand.name',
                ],
                [
                    'name' => 'url',
                    'key' => 'product.brand.url',
                    'short_code' => 'product.brand.url',
                ],
            ],
            'dimension' => [
                [
                    'name' => 'uuid',
                    'key' => 'product.dimension.uuid',
                    'short_code' => 'product.brand.uuid',
                ],
                [
                    'name' => 'length',
                    'key' => 'product.dimension.length',
                    'short_code' => 'product.dimension.length',
                ],
                [
                    'name' => 'width',
                    'key' => 'product.dimension.width',
                    'short_code' => 'product.dimension.width',
                ],
                [
                    'name' => 'height',
                    'key' => 'product.dimension.height',
                    'short_code' => 'product.dimension.height',
                ],                [
                    'name' => 'height',
                    'key' => 'product.dimension.height',
                    'short_code' => 'product.dimension.height',
                ],
                [
                    'name' => 'weight',
                    'key' => 'product.dimension.weight',
                    'short_code' => 'product.dimension.weight',
                ],
                [
                    'name' => 'unit_type_weight',
                    'key' => 'product.dimension.unit_type_weight',
                    'short_code' => 'product.dimension.unit_type_weight',
                ],
                [
                    'name' => 'unit_type_dimension',
                    'key' => 'product.dimension.unit_type_dimension',
                    'short_code' => 'product.dimension.unit_type_dimension',
                ],
            ],

        ];

        //create parent first
        foreach ($parentShortCodes as $parentShortCode) {
            $shortCodeGroups = $parentShortCode['short_code_groups'];
            unset($parentShortCode['short_code_groups']);
            $shortCode = WebsitePageShortCode::updateOrCreate(['key' => $parentShortCode['key']], $parentShortCode);
            $shortCodeGroupUuid = ShortCodeGroup::whereIn('key', $shortCodeGroups)->get()->pluck('uuid')->toArray();
            $shortCode->shortCodeGroups()->syncWithoutDetaching($shortCodeGroupUuid);
        }
        $articleList = WebsitePageShortCode::where(['key' => 'article_list'])->first();
        $categoryList = WebsitePageShortCode::where(['key' => 'category_list'])->first();
        $childrenCategoryList = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
        $grandChildrenCategoryList = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
        $specificArticleList = WebsitePageShortCode::where(['key' => 'specific_article_list'])->first();

        $productList = WebsitePageShortCode::where(['key' => 'product_list'])->first();
        $productCategoryList = WebsitePageShortCode::where(['key' => 'category_list'])->first();
        $childrenProductCategoryList = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
        $grandChildrenProductCategoryList = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
        $specificProductList = WebsitePageShortCode::where(['key' => 'specific_product_list'])->first();

        //create sort (order by)
        foreach ($sorts as $key => $sort) {
            if ($key == 'article_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$articleList->uuid]], $sort));
            } elseif ($key == 'category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$categoryList->uuid]], $sort));
            } elseif ($key == 'children_category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$childrenCategoryList->uuid]], $sort));
            } elseif ($key == 'grand_children_category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$grandChildrenCategoryList->uuid]], $sort));
            }
            //sort of product
            elseif ($key == 'product_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$productList->uuid]], $sort));
            } elseif ($key == 'product_category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$categoryList->uuid]], $sort));
            } elseif ($key == 'children_product_category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$childrenCategoryList->uuid]], $sort));
            } elseif ($key == 'grand_children_product_category_sort') {
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuids' => [$grandChildrenCategoryList->uuid]], $sort));
            }
        }

        foreach ($filters as $key => $filter) {
            if ($key == 'article_filter') {
                WebsitePageShortCode::updateOrCreate(['key' => $filter['key']], array_merge(['parent_uuids' => [$articleList->uuid]], $filter));
            } elseif ($key == 'product_filter') {
                WebsitePageShortCode::updateOrCreate(['key' => $filter['key']], array_merge(['parent_uuids' => [$productList->uuid]], $filter));
            }
        }

        foreach ($specifics as $key => $specific) {
            if ($key == 'article') {
                WebsitePageShortCode::updateOrCreate(['key' => $specific['key']], array_merge(['parent_uuids' => [$specificArticleList->uuid]], $specific));
            } elseif ($key == 'product') {
                WebsitePageShortCode::updateOrCreate(['key' => $specific['key']], array_merge(['parent_uuids' => [$specificProductList->uuid]], $specific));
            }
        }

        foreach ($elements as $key => $element) {
            if ($key == 'article') {
                $shortCodeGroups = $element['short_code_groups'];
                unset($element['short_code_groups']);

                $articleElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$articleList->uuid, $specificArticleList->uuid]], $element));
                $shortCodeGroupUuid = ShortCodeGroup::whereIn('key', $shortCodeGroups)->get()->pluck('uuid')->toArray();
                $articleElement->shortCodeGroups()->syncWithoutDetaching($shortCodeGroupUuid);
            } elseif ($key == 'category') {
                $shortCodeGroups = $element['short_code_groups'];
                unset($element['short_code_groups']);
                $parent = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                $categoryElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
                $shortCodeGroupUuid = ShortCodeGroup::whereIn('key', $shortCodeGroups)->get()->pluck('uuid')->toArray();
                $categoryElement->shortCodeGroups()->syncWithoutDetaching($shortCodeGroupUuid);
            } elseif ($key == 'children_category') {
                $parent = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                $childrenCategoryElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
            } elseif ($key == 'grand_children_category') {
                $parent = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
            }
            //element product
            elseif ($key == 'product') {
                $shortCodeGroups = $element['short_code_groups'];
                unset($element['short_code_groups']);

                $productElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$productList->uuid, $specificProductList->uuid]], $element));
                $shortCodeGroupUuid = ShortCodeGroup::whereIn('key', $shortCodeGroups)->get()->pluck('uuid')->toArray();
                $productElement->shortCodeGroups()->syncWithoutDetaching($shortCodeGroupUuid);
            } elseif ($key == 'product_category') {
                $shortCodeGroups = $element['short_code_groups'];
                unset($element['short_code_groups']);
                $parent = WebsitePageShortCode::where(['key' => 'product_category_list'])->first();
                $productCategoryElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
                $shortCodeGroupUuid = ShortCodeGroup::whereIn('key', $shortCodeGroups)->get()->pluck('uuid')->toArray();
                $productCategoryElement->shortCodeGroups()->syncWithoutDetaching($shortCodeGroupUuid);
            } elseif ($key == 'children_product_category') {
                $parent = WebsitePageShortCode::where(['key' => 'children_product_category_list'])->first();
                $childrenProductCategoryElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
            } elseif ($key == 'grand_children_product_category') {
                $parent = WebsitePageShortCode::where(['key' => 'grand_children_product_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$parent->uuid]], $element));
            } elseif ($key == 'dimension') {
                $dimensionElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$productElement->uuid]], $element));
            } elseif ($key == 'brand') {
                $brandElement = WebsitePageShortCode::updateOrCreate(['key' => $element['key']], array_merge(['parent_uuids' => [$productElement->uuid]], $element));
            }
        }
        $articleList->update(['parent_uuids' => [$categoryElement->uuid, $childrenCategoryElement->uuid]]);
        $childrenCategoryList->update(['parent_uuids' => [$categoryElement->uuid]]);

        $productList->update(['parent_uuids' => [$productCategoryElement->uuid, $childrenProductCategoryElement->uuid]]);
        $childrenProductCategoryList->update(['parent_uuids' => [$productCategoryElement->uuid]]);

        //create sort order (asc or desc)
        foreach ($sortOrders as $key => $sortOrder) {
            if ($key == 'article_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'article_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$parentSort->uuid]], $sortOrder));
            } elseif ($key == 'category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$parentSort->uuid]], $sortOrder));
            } elseif ($key == 'children_category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$parentSort->uuid]], $sortOrder));
            } elseif ($key == 'grand_children_category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$parentSort->uuid]], $sortOrder));
            }
            //Sort of product
            elseif ($key == 'product_sort_order') {
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$productList->uuid]], $sortOrder));
            } elseif ($key == 'product_category_sort_order') {
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$productCategoryList->uuid]], $sortOrder));
            } elseif ($key == 'children_product_category_sort_order') {
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$childrenProductCategoryList->uuid]], $sortOrder));
            } elseif ($key == 'grand_children_product_category_sort_order') {
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuids' => [$grandChildrenProductCategoryList->uuid]], $sortOrder));
            }
        }

        //create count (per page)
        foreach ($counts as $key => $count) {
            if ($key == 'article_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'article_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$parentCount->uuid]], $count));
            } elseif ($key == 'category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$parentCount->uuid]], $count));
            } elseif ($key == 'children_category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$parentCount->uuid]], $count));
            } elseif ($key == 'grand_children_category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$parentCount->uuid]], $count));
            }
            //count of product
            elseif ($key == 'product_count') {
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$productList->uuid]], $count));
            } elseif ($key == 'product_category_count') {
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$productCategoryList->uuid]], $count));
            } elseif ($key == 'product_children_category_count') {
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$childrenProductCategoryList->uuid]], $count));
            } elseif ($key == 'grand_children_product_category_count') {
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuids' => [$grandChildrenProductCategoryList->uuid]], $count));
            }
        }

        //create short code
        foreach ($shortCodes as $key => $shortCode) {
            if ($key == 'article') {
                foreach ($shortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuids' => [$parent->uuid]], $articleShortCode));
                }
            } elseif ($key == 'category') {
                foreach ($shortCode as $categoryShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categoryShortCode['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categoryShortCode));
                }
            } elseif ($key == 'children_category') {
                foreach ($shortCode as $childrenCategory) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategory['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategory));
                }
            } elseif ($key == 'grand_children_category') {
                foreach ($shortCode as $grandChildren) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildren['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildren));
                }
            } elseif ($key == 'article-sort') {
                foreach ($shortCode as $articleSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleSort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $articleSort));
                }
            } elseif ($key == 'category-sort') {
                foreach ($shortCode as $categorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categorySort));
                }
            } elseif ($key == 'children-category-sort') {
                foreach ($shortCode as $childrenCategorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategorySort));
                }
            } elseif ($key == 'grand-children-category-sort') {
                foreach ($shortCode as $grandChildrenSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildrenSort));
                }
            } elseif ($key == 'article-sort-order') {
                foreach ($shortCode as $articleSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleSortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $articleSortOrder));
                }
            } elseif ($key == 'category-sort-order') {
                foreach ($shortCode as $categorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categorySortOrder));
                }
            } elseif ($key == 'children-category-sort-order') {
                foreach ($shortCode as $childrenCategorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategorySortOrder));
                }
            } elseif ($key == 'grand-children-category-sort-order') {
                foreach ($shortCode as $grandChildrenSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildrenSortOrder));
                }
            }

            //short code of product
            elseif ($key == 'product') {
                foreach ($shortCode as $productShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'product'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $productShortCode['key']], array_merge(['parent_uuids' => [$parent->uuid]], $productShortCode));
                }
            } elseif ($key == 'product_category') {
                foreach ($shortCode as $categoryShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'product_category_element'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categoryShortCode['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categoryShortCode));
                }
            } elseif ($key == 'children_product_category') {
                foreach ($shortCode as $childrenCategory) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_product_category_element'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategory['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategory));
                }
            } elseif ($key == 'grand_children_product_category') {
                foreach ($shortCode as $grandChildren) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_product_category_element'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildren['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildren));
                }
            } elseif ($key == 'product-sort') {
                foreach ($shortCode as $productSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'product_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $productSort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $productSort));
                }
            } elseif ($key == 'product-category-sort') {
                foreach ($shortCode as $categorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'product_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categorySort));
                }
            } elseif ($key == 'children-product-category-sort') {
                foreach ($shortCode as $childrenCategorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_product_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategorySort));
                }
            } elseif ($key == 'grand-children-product-category-sort') {
                foreach ($shortCode as $grandChildrenSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_product_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSort['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildrenSort));
                }
            } elseif ($key == 'product-sort-order') {
                foreach ($shortCode as $productSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'product_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $productSortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $productSortOrder));
                }
            } elseif ($key == 'product-category-sort-order') {
                foreach ($shortCode as $categorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'product_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $categorySortOrder));
                }
            } elseif ($key == 'children-product-category-sort-order') {
                foreach ($shortCode as $childrenCategorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_product_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $childrenCategorySortOrder));
                }
            } elseif ($key == 'grand-children-product-category-sort-order') {
                foreach ($shortCode as $grandChildrenSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_product_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSortOrder['key']], array_merge(['parent_uuids' => [$parent->uuid]], $grandChildrenSortOrder));
                }
            } elseif ($key == 'dimension') {
                foreach ($shortCode as $grandChildrenSortOrder) {
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSortOrder['key']], array_merge(['parent_uuids' => [$dimensionElement->uuid]], $grandChildrenSortOrder));
                }
            } elseif ($key == 'brand') {
                foreach ($shortCode as $grandChildrenSortOrder) {
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSortOrder['key']], array_merge(['parent_uuids' => [$brandElement->uuid]], $grandChildrenSortOrder));
                }
            }
        }


    }
}
