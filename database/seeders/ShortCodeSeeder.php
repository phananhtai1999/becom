<?php

namespace Database\Seeders;

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
                'name' => 'article element',
                'key' => 'article',
                'short_code' => 'article_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'category element',
                'key' => 'category',
                'short_code' => 'category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'children category',
                'key' => 'children_category',
                'short_code' => 'children_category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'grand children category',
                'key' => 'grand_children_category',
                'short_code' => 'grand_children_category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'article list',
                'key' => 'article_list',
                'short_code' => 'article_list',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'category list',
                'key' => 'category_list',
                'short_code' => 'category_list',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'children category list',
                'key' => 'children_category_list',
                'short_code' => 'children_category_list',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'grand children category',
                'key' => 'grand_children_category_list',
                'short_code' => 'grand_children_category_list',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'count',
                'key' => 'count',
                'short_code' => 'count_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
        ];
        $sorts = [
            'article_sort' =>
                [
                    'name' => 'sort for article',
                    'key' => 'article_sort',
                    'short_code' => 'article-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            'category_sort' =>
                [
                    'name' => 'sort for category',
                    'key' => 'category_sort',
                    'short_code' => 'category-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            'children_category_sort' => [
                'name' => 'sort for children category',
                'key' => 'children_category_sort',
                'short_code' => 'children-category-sort',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            'grand_children_category_sort' => [
                'name' => 'sort for grand children category',
                'key' => 'grand_children_category_sort',
                'short_code' => 'grand-children-category-sort',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
        ];
        $sortOrders = [
                'article_sort_order' => [
                    'name' => 'sort order for article',
                    'key' => 'article_sort_order',
                    'short_code' => 'article-sort-order',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'category_sort_order' => [
                    'name' => 'sort order for category',
                    'key' => 'category_sort_order',
                    'short_code' => 'category-sort-order',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'children_category_sort_order' => [
                    'name' => 'sort order for children category',
                    'key' => 'children_category_sort_order',
                    'short_code' => 'children-category-sort-order',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'grand_children_category_sort_order' => [
                    'name' => 'sort order for grand children category',
                    'key' => 'grand_children_category_sort_order',
                    'short_code' => 'grand-children-category-sort-order',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
        ];
        $counts = [
            'article_category' => [
                'article_count' => [
                    'name' => 'count for article',
                    'key' => 'article_count',
                    'short_code' => 'data-article-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'category_count' => [
                    'name' => 'count for category',
                    'key' => 'category_count',
                    'short_code' => 'data-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'children_category_count' => [
                    'name' => 'count for children category',
                    'key' => 'children_category_count',
                    'short_code' => 'data-children-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                'grand_children_category_count' => [
                    'name' => 'count for grand children category ',
                    'key' => 'grand_children_category_count',
                    'short_code' => 'data-grand-children-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
        ];
        $shortCodes = [
            'article' => [
                [
                    'name' => 'uuid',
                    'key' => 'article.uuid',
                    'short_code' => 'article.uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'slug',
                    'key' => 'article.slug',
                    'short_code' => 'article.slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'article category uuid',
                    'key' => 'article.article_category_uuid',
                    'short_code' => 'article.article_category_uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'title',
                    'key' => 'article.title',
                    'short_code' => 'article.title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'content',
                    'key' => 'article.content',
                    'short_code' => 'article.content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'image',
                    'key' => 'article.image',
                    'short_code' => 'article.image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'video',
                    'key' => 'article.video',
                    'short_code' => 'article.video',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'keyword',
                    'key' => 'article.keyword',
                    'short_code' => 'article.keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'description',
                    'key' => 'article.description',
                    'short_code' => 'article.description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'short content',
                    'key' => 'article.short_content',
                    'short_code' => 'article.short_content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'category' => [
                [
                    'name' => 'uuid',
                    'key' => 'category.uuid',
                    'short_code' => 'category.uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'slug',
                    'key' => 'category.slug',
                    'short_code' => 'category.slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'title',
                    'key' => 'category.title',
                    'short_code' => 'category.title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'content',
                    'key' => 'category.content',
                    'short_code' => 'category.content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'image',
                    'key' => 'category.image',
                    'short_code' => 'category.image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'feature image',
                    'key' => 'category.feature_image',
                    'short_code' => 'category.feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'keyword',
                    'key' => 'category.keyword',
                    'short_code' => 'category.keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'description',
                    'key' => 'category.description',
                    'short_code' => 'category.description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'short content',
                    'key' => 'category.short_content',
                    'short_code' => 'category.short_content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'children_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'children_category.uuid',
                    'short_code' => 'children_category.uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'slug',
                    'key' => 'children_category.slug',
                    'short_code' => 'children_category.slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'title',
                    'key' => 'children_category.title',
                    'short_code' => 'children_category.title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'content',
                    'key' => 'children_category.content',
                    'short_code' => 'children_category.content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'image',
                    'key' => 'children_category.image',
                    'short_code' => 'children_category.image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'feature image',
                    'key' => 'children_category.feature_image',
                    'short_code' => 'children_category.feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'keyword',
                    'key' => 'children_category.keyword',
                    'short_code' => 'children_category.keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'description',
                    'key' => 'children_category.description',
                    'short_code' => 'children_category.description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'short content',
                    'key' => 'children_category.short_content',
                    'short_code' => 'children_category.short_content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'grand_children_category' => [
                [
                    'name' => 'uuid',
                    'key' => 'grand_children_category.uuid',
                    'short_code' => 'grand_children_category.uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'slug',
                    'key' => 'grand_children_category.slug',
                    'short_code' => 'children_category.slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'title',
                    'key' => 'grand_children_category.title',
                    'short_code' => 'grand_children_category.title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'content',
                    'key' => 'grand_children_category.content',
                    'short_code' => 'grand_children_category.content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'image',
                    'key' => 'grand_children_category.image',
                    'short_code' => 'grand_children_category.image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'feature image',
                    'key' => 'grand_children_category.feature_image',
                    'short_code' => 'grand_children_category.feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'keyword',
                    'key' => 'grand_children_category.keyword',
                    'short_code' => 'grand_children_category.keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'description',
                    'key' => 'grand_children_category.description',
                    'short_code' => 'grand_children_category.description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'short content',
                    'key' => 'grand_children_category.short_content',
                    'short_code' => 'grand_children_category.short_content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'article-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'article_sort.uuid',
                    'short_code' => 'uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'article_sort.slug',
                    'short_code' => 'slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'article_sort.title',
                    'short_code' => 'title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by content',
                    'key' => 'article_sort.content',
                    'short_code' => 'content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'article_sort.image',
                    'short_code' => 'image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by video',
                    'key' => 'article_sort.video',
                    'short_code' => 'video',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'article_sort.keyword',
                    'short_code' => 'keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'article_sort.description',
                    'short_code' => 'description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by short content',
                    'key' => 'article_sort.short_content',
                    'short_code' => 'short_content',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'article_sort.created_at',
                    'short_code' => 'created_at',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'category_sort.uuid',
                    'short_code' => 'uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'category_sort.slug',
                    'short_code' => 'slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'category_sort.title',
                    'short_code' => 'title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'category_sort.image',
                    'short_code' => 'image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'category_sort.feature_image',
                    'short_code' => 'feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'category_sort.keyword',
                    'short_code' => 'keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'category_sort.description',
                    'short_code' => 'description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'category_sort.created_at',
                    'short_code' => 'created_at',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'children-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'children_category_sort.uuid',
                    'short_code' => 'uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'children_category_sort.slug',
                    'short_code' => 'slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'children_category_sort.title',
                    'short_code' => 'title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'children_category_sort.image',
                    'short_code' => 'image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'children_category_sort.feature_image',
                    'short_code' => 'feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'children_category_sort.keyword',
                    'short_code' => 'keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'children_category_sort.description',
                    'short_code' => 'description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'children_category_sort.created_at',
                    'short_code' => 'created_at',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'grand-children-category-sort' => [
                [
                    'name' => 'sort by uuid',
                    'key' => 'grand_children_category_sort.uuid',
                    'short_code' => 'uuid',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by slug',
                    'key' => 'grand_children_category_sort.slug',
                    'short_code' => 'slug',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by title',
                    'key' => 'grand_children_category_sort.title',
                    'short_code' => 'title',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by image',
                    'key' => 'grand_children_category_sort.image',
                    'short_code' => 'image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by feature image',
                    'key' => 'grand_children_category_sort.feature_image',
                    'short_code' => 'feature_image',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by keyword',
                    'key' => 'grand_children_category_sort.keyword',
                    'short_code' => 'keyword',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by description',
                    'key' => 'grand_children_category_sort.description',
                    'short_code' => 'description',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort by created at',
                    'key' => 'grand_children_category_sort.created_at',
                    'short_code' => 'created_at',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],
            'article-sort-order' => [
                [
                    'name' => 'sort order by desc for article',
                    'key' => 'article_sort_order.desc',
                    'short_code' => 'desc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort order by asc for article',
                    'key' => 'article_sort_order.asc',
                    'short_code' => 'asc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ]
            ],
            'category-sort-order' => [
                [
                    'name' => 'sort order by desc for category',
                    'key' => 'category_sort_order.desc',
                    'short_code' => 'desc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort order by asc for category',
                    'key' => 'category_sort_order.asc',
                    'short_code' => 'asc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ]
            ],
            'children-category-sort-order' => [
                [
                    'name' => 'sort order by desc for children category',
                    'key' => 'children_category_sort_order.desc',
                    'short_code' => 'desc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'children_category_sort_order.asc',
                    'short_code' => 'asc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ]
            ],
            'grand-children-category-sort-order' => [
                [
                    'name' => 'sort order by desc for grand children category',
                    'key' => 'grand_children_category_sort_order.desc',
                    'short_code' => 'desc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort order by asc for children category',
                    'key' => 'grand_children_category_sort_order.asc',
                    'short_code' => 'asc',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ]
            ],
        ];

        //create parent first
        foreach ($parentShortCodes as $parentShortCode) {
            WebsitePageShortCode::updateOrCreate(['key' => $parentShortCode['key']], $parentShortCode);
        }

        //create sort (order by)
        foreach ($sorts as $key => $sort) {
            if ($key == 'article_sort') {
                $parentSort = WebsitePageShortCode::where(['key' => 'article_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sort));
            } elseif ($key == 'category_sort') {
                $parentSort = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sort));
            } elseif ($key == 'children_category_sort') {
                $parentSort = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sort));
            } elseif ($key == 'grand_children_category_sort') {
                $parentSort = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sort));
            }
        }

        //create sort order (asc or desc)
        foreach ($sortOrders as $key => $sortOrder) {
            if ($key == 'article_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'article_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sortOrder));
            } elseif ($key == 'category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sortOrder));
            } elseif ($key == 'children_category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sortOrder));
            } elseif ($key == 'grand_children_category_sort_order') {
                $parentSort = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $sortOrder['key']], array_merge(['parent_uuid' => $parentSort->uuid], $sortOrder));
            }
        }

        //create count (per page)
        foreach ($counts as $key => $count) {
            if ($key == 'article_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'article_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuid' => $parentCount->uuid], $count));
            } elseif ($key == 'category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuid' => $parentCount->uuid], $count));
            } elseif ($key == 'children_category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuid' => $parentCount->uuid], $count));
            } elseif ($key == 'grand_children_category_count') {
                $parentCount = WebsitePageShortCode::where(['key' => 'grand_children_category_list'])->first();
                WebsitePageShortCode::updateOrCreate(['key' => $count['key']], array_merge(['parent_uuid' => $parentCount->uuid], $count));
            }
        }

        //create short code
        foreach ($shortCodes as $key => $shortCode) {
            if ($key == 'article') {
                foreach ($shortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'category') {
                foreach ($shortCode as $categoryShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categoryShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $categoryShortCode));
                }
            } elseif ($key == 'children_category') {
                foreach ($shortCode as $childrenCategory) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategory['key']], array_merge(['parent_uuid' => $parent->uuid], $childrenCategory));
                }
            } elseif ($key == 'grand_children_category') {
                foreach ($shortCode as $grandChildren) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildren['key']], array_merge(['parent_uuid' => $parent->uuid], $grandChildren));
                }
            } elseif ($key == 'article-sort') {
                foreach ($shortCode as $articleSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleSort['key']], array_merge(['parent_uuid' => $parent->uuid], $articleSort));
                }
            } elseif ($key == 'category-sort') {
                foreach ($shortCode as $categorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySort['key']], array_merge(['parent_uuid' => $parent->uuid], $categorySort));
                }
            } elseif ($key == 'children-category-sort') {
                foreach ($shortCode as $childrenCategorySort) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySort['key']], array_merge(['parent_uuid' => $parent->uuid], $childrenCategorySort));
                }
            } elseif ($key == 'grand-children-category-sort') {
                foreach ($shortCode as $grandChildrenSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSort['key']], array_merge(['parent_uuid' => $parent->uuid], $grandChildrenSort));
                }
            } elseif ($key == 'article-sort-order') {
                foreach ($shortCode as $articleSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleSortOrder['key']], array_merge(['parent_uuid' => $parent->uuid], $articleSortOrder));
                }
            } elseif ($key == 'category-sort-order') {
                foreach ($shortCode as $categorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categorySortOrder['key']], array_merge(['parent_uuid' => $parent->uuid], $categorySortOrder));
                }
            } elseif ($key == 'children-category-sort-order') {
                foreach ($shortCode as $childrenCategorySortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'children_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategorySortOrder['key']], array_merge(['parent_uuid' => $parent->uuid], $childrenCategorySortOrder));
                }
            } elseif ($key == 'grand-children-category-sort-order') {
                foreach ($shortCode as $grandChildrenSortOrder) {
                    $parent = WebsitePageShortCode::where(['key' => 'grand_children_category_sort_order'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $grandChildrenSortOrder['key']], array_merge(['parent_uuid' => $parent->uuid], $grandChildrenSortOrder));
                }
            }
        }

        
    }
}
