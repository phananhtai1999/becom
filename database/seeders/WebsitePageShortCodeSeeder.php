<?php

namespace Database\Seeders;

use App\Models\WebsitePageShortCode;
use Illuminate\Database\Seeder;

class WebsitePageShortCodeSeeder extends Seeder
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
                'name' => 'article inside article category',
                'key' => 'article_category.article',
                'short_code' => 'article_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'category inside article category',
                'key' => 'article_category.category',
                'short_code' => 'category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'children category inside article category',
                'key' => 'article_category.children_category',
                'short_code' => 'children_category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'grand children category inside article category',
                'key' => 'article_category.grand_children_category',
                'short_code' => 'grand_children_category_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'sort inside article category',
                'key' => 'article_category.sort',
                'short_code' => 'sort_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],
            [
                'name' => 'count inside article category',
                'key' => 'article_category.count',
                'short_code' => 'count_element',
                'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
            ],

            //home_detail
            [
                'name' => 'category inside home article',
                'key' => 'home_articles.category',
                'short_code' => 'category_element',
                'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
            ],
            [
                'name' => 'article inside home article',
                'key' => 'home_articles.article',
                'short_code' => 'article_element',
                'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
            ],
            [
                'name' => 'children category inside home article',
                'key' => 'home_articles.children_category',
                'short_code' => 'children_category_element',
                'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
            ],
            [
                'name' => 'sort inside home article',
                'key' => 'home_articles.sort',
                'short_code' => 'sort_element',
                'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
            ],
            [
                'name' => 'count inside home article',
                'key' => 'home_articles.count',
                'short_code' => 'count_element',
                'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
            ],
        ];

        $sorts = [
            'article_category' => [
                [
                    'name' => 'sort for article inside article category',
                    'key' => 'article_category.article_sort',
                    'short_code' => 'article-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort for category inside article category',
                    'key' => 'article_category.category_sort',
                    'short_code' => 'category-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort for children category inside article category',
                    'key' => 'article_category.children_category_sort',
                    'short_code' => 'children-category-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'sort for grand children category inside article category',
                    'key' => 'article_category.grand_children_category_sort',
                    'short_code' => 'grand-children-category-sort',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],

            //homes article
            'home_articles' => [
                [
                    'name' => 'sort for article inside home articles',
                    'key' => 'home_articles.article_sort',
                    'short_code' => 'article-sort',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
                [
                    'name' => 'sort for category inside home articles',
                    'key' => 'home_articles.category_sort',
                    'short_code' => 'category-sort',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
                [
                    'name' => 'sort for children category inside home articles',
                    'key' => 'home_articles.children_category_sort',
                    'short_code' => 'children-category-sort',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
            ]
        ];
        $counts = [
            'article_category' => [
                [
                    'name' => 'count for article inside article category',
                    'key' => 'article_category.article_count',
                    'short_code' => 'data-article-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'count for category inside article category',
                    'key' => 'article_category.category_count',
                    'short_code' => 'data-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'count for children category inside article category',
                    'key' => 'article_category.children_category_count',
                    'short_code' => 'data-children-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
                [
                    'name' => 'count for grand children category inside article category',
                    'key' => 'article_category.grand_children_category_count',
                    'short_code' => 'data-grand-children-category-count',
                    'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                ],
            ],

            //homes article
            'home_articles' => [
                [
                    'name' => 'count for article inside home articles',
                    'key' => 'home_articles.article_count',
                    'short_code' => 'data-article-count',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
                [
                    'name' => 'count for category inside home articles',
                    'key' => 'home_articles.category_count',
                    'short_code' => 'data-category-count',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
                [
                    'name' => 'count for children category inside home articles',
                    'key' => 'home_articles.children_category_count',
                    'short_code' => 'data-children-category-count',
                    'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                ],
            ]
        ];
        $shortCodes = [
            'article_category' => [
                'article' => [
                    [
                        'name' => 'uuid',
                        'key' => 'article_category.article.uuid',
                        'short_code' => 'article.uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'article_category.article.slug',
                        'short_code' => 'article.slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'article_category.article.title',
                        'short_code' => 'article.title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'article_category.article.content',
                        'short_code' => 'article.content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'article_category.article.image',
                        'short_code' => 'article.image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'video',
                        'key' => 'article_category.article.video',
                        'short_code' => 'article.video',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'article_category.article.keyword',
                        'short_code' => 'article.keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'article_category.article.description',
                        'short_code' => 'article.description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'article_category.article.short_content',
                        'short_code' => 'article.short_content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'category' => [
                    [
                        'name' => 'uuid',
                        'key' => 'article_category.category.uuid',
                        'short_code' => 'category.uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'article_category.category.slug',
                        'short_code' => 'category.slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'article_category.category.title',
                        'short_code' => 'category.title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'article_category.category.content',
                        'short_code' => 'category.content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'article_category.category.image',
                        'short_code' => 'category.image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'feature image',
                        'key' => 'article_category.category.feature_image',
                        'short_code' => 'category.feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'article_category.category.keyword',
                        'short_code' => 'category.keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'article_category.category.description',
                        'short_code' => 'category.description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'article_category.category.short_content',
                        'short_code' => 'category.short_content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'children_category' => [
                    [
                        'name' => 'uuid',
                        'key' => 'article_category.children_category.uuid',
                        'short_code' => 'children_category.uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'article_category.children_category.slug',
                        'short_code' => 'children_category.slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'article_category.children_category.title',
                        'short_code' => 'children_category.title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'article_category.children_category.content',
                        'short_code' => 'children_category.content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'article_category.children_category.image',
                        'short_code' => 'children_category.image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'feature image',
                        'key' => 'article_category.children_category.feature_image',
                        'short_code' => 'children_category.feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'article_category.children_category.keyword',
                        'short_code' => 'children_category.keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'article_category.children_category.description',
                        'short_code' => 'children_category.description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'article_category.children_category.short_content',
                        'short_code' => 'children_category.short_content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'grand_children_category' => [
                    [
                        'name' => 'uuid',
                        'key' => 'article_category.grand_children_category.uuid',
                        'short_code' => 'grand_children_category.uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'article_category.children_category.slug',
                        'short_code' => 'children_category.slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'article_category.grand_children_category.title',
                        'short_code' => 'grand_children_category.title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'article_category.grand_children_category.content',
                        'short_code' => 'grand_children_category.content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'article_category.grand_children_category.image',
                        'short_code' => 'grand_children_category.image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'feature image',
                        'key' => 'article_category.grand_children_category.feature_image',
                        'short_code' => 'grand_children_category.feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'article_category.grand_children_category.keyword',
                        'short_code' => 'grand_children_category.keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'article_category.grand_children_category.description',
                        'short_code' => 'grand_children_category.description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'article_category.grand_children_category.short_content',
                        'short_code' => 'grand_children_category.short_content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'article-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'article_category.article_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'article_category.article_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'article_category.article_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by content',
                        'key' => 'article_category.article_sort.content',
                        'short_code' => 'content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'article_category.article_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by video',
                        'key' => 'article_category.article_sort.video',
                        'short_code' => 'video',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'article_category.article_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'article_category.article_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by short content',
                        'key' => 'article_category.article_sort.short_content',
                        'short_code' => 'short_content',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'article_category.article_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'category-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'article_category.category_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'article_category.category_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'article_category.category_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'article_category.category_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by feature image',
                        'key' => 'article_category.category_sort.feature_image',
                        'short_code' => 'feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'article_category.category_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'article_category.category_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'article_category.category_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'children-category-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'article_category.children_category_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'article_category.children_category_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'article_category.children_category_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'article_category.children_category_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by feature image',
                        'key' => 'article_category.children_category_sort.feature_image',
                        'short_code' => 'feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'article_category.children_category_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'article_category.children_category_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'article_category.children_category_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
                'grand-children-category-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'article_category.grand_children_category_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'article_category.grand_children_category_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'article_category.grand_children_category_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'article_category.grand_children_category_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by feature image',
                        'key' => 'article_category.grand_children_category_sort.feature_image',
                        'short_code' => 'feature_image',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'article_category.grand_children_category_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'article_category.grand_children_category_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'article_category.grand_children_category_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::ARTICLE_CATEGORY_TYPE
                    ],
                ],
            ],
            'article_detail' => [
                [
                    'name' => 'uuid',
                    'key' => 'article_detail.article.uuid',
                    'short_code' => 'article.uuid',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'title',
                    'key' => 'article_detail.article.title',
                    'short_code' => 'article.title',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'content',
                    'key' => 'article_detail.article.content',
                    'short_code' => 'article.content',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'image',
                    'key' => 'article_detail.article.image',
                    'short_code' => 'article.image',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'video',
                    'key' => 'article_detail.article.video',
                    'short_code' => 'article.video',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'keyword',
                    'key' => 'article_detail.article.keyword',
                    'short_code' => 'article.keyword',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'description',
                    'key' => 'article_detail.article.description',
                    'short_code' => 'article.description',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
                [
                    'name' => 'short content',
                    'key' => 'article_detail.article.short_content',
                    'short_code' => 'article.short_content',
                    'type' => WebsitePageShortCode::ARTICLE_DETAIL_TYPE
                ],
            ],
            'home_articles' => [
                'article' => [
                    [
                        'name' => 'uuid',
                        'key' => 'home_articles.article.uuid',
                        'short_code' => 'article.uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'home_articles.article.slug',
                        'short_code' => 'article.slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'home_articles.article.title',
                        'short_code' => 'article.title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'home_articles.article.content',
                        'short_code' => 'article.content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'home_articles.article.image',
                        'short_code' => 'article.image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'video',
                        'key' => 'home_articles.article.video',
                        'short_code' => 'article.video',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'home_articles.article.keyword',
                        'short_code' => 'article.keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'home_articles.article.description',
                        'short_code' => 'article.description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'home_articles.article.short_content',
                        'short_code' => 'article.short_content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
                'category' => [
                    [
                        'name' => 'uuid',
                        'key' => 'home_articles.category.uuid',
                        'short_code' => 'category.uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'home_articles.category.slug',
                        'short_code' => 'category.slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'home_articles.category.title',
                        'short_code' => 'category.title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'home_articles.category.content',
                        'short_code' => 'category.content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'home_articles.category.image',
                        'short_code' => 'category.image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'feature image',
                        'key' => 'home_articles.category.feature_image',
                        'short_code' => 'category.feature_image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'home_articles.category.keyword',
                        'short_code' => 'category.keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'home_articles.category.description',
                        'short_code' => 'category.description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'home_articles.category.short_content',
                        'short_code' => 'category.short_content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
                'children_category' => [
                    [
                        'name' => 'uuid',
                        'key' => 'home_articles.children_category.uuid',
                        'short_code' => 'children_category.uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'slug',
                        'key' => 'home_articles.children_category.slug',
                        'short_code' => 'children_category.slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'title',
                        'key' => 'home_articles.children_category.title',
                        'short_code' => 'children_category.title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'content',
                        'key' => 'home_articles.children_category.content',
                        'short_code' => 'children_category.content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'image',
                        'key' => 'home_articles.children_category.image',
                        'short_code' => 'children_category.image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'feature image',
                        'key' => 'home_articles.children_category.feature_image',
                        'short_code' => 'children_category.feature_image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'keyword',
                        'key' => 'home_articles.children_category.keyword',
                        'short_code' => 'children_category.keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'description',
                        'key' => 'home_articles.children_category.description',
                        'short_code' => 'children_category.description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'short content',
                        'key' => 'home_articles.children_category.short_content',
                        'short_code' => 'children_category.short_content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
                'article-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'home_articles.article_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'home_articles.article_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'home_articles.article_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by content',
                        'key' => 'home_articles.article_sort.content',
                        'short_code' => 'content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'home_articles.article_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by video',
                        'key' => 'home_articles.article_sort.video',
                        'short_code' => 'video',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'home_articles.article_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'home_articles.article_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by short content',
                        'key' => 'home_articles.article_sort.short_content',
                        'short_code' => 'short_content',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'home_articles.article_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
                'category-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'home_articles.category_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'home_articles.category_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'home_articles.category_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'home_articles.article_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by feature image',
                        'key' => 'home_articles.category_sort.feature_image',
                        'short_code' => 'feature_image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'home_articles.category_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'home_articles.category_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'home_articles.category_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
                'children-category-sort' => [
                    [
                        'name' => 'sort by uuid',
                        'key' => 'home_articles.children_category_sort.uuid',
                        'short_code' => 'uuid',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by slug',
                        'key' => 'home_articles.children_category_sort.slug',
                        'short_code' => 'slug',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by title',
                        'key' => 'home_articles.children_category_sort.title',
                        'short_code' => 'title',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by image',
                        'key' => 'home_articles.children_category_sort.image',
                        'short_code' => 'image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by feature image',
                        'key' => 'home_articles.children_category_sort.feature_image',
                        'short_code' => 'feature_image',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by keyword',
                        'key' => 'home_articles.children_category_sort.keyword',
                        'short_code' => 'keyword',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by description',
                        'key' => 'home_articles.children_category_sort.description',
                        'short_code' => 'description',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                    [
                        'name' => 'sort by created at',
                        'key' => 'home_articles.children_category_sort.created_at',
                        'short_code' => 'created_at',
                        'type' => WebsitePageShortCode::HOME_ARTICLES_TYPE
                    ],
                ],
            ],
            'element_count' => [
                'data-grand-children-category-count',
                'data-children-category-count',
                'data-article-count',
                'data-category-count',
            ],
            'category-sort' => [
                'created_at',
                'uuid',
                'image',
                'slug',
                'parent_uuid',
                'publish_status',
                'title',
                'keyword',
                'description',
                'feature_image',
            ],
            'children-category-sort' => [
                'created_at',
                'uuid',
                'image',
                'slug',
                'parent_uuid',
                'publish_status',
                'title',
                'keyword',
                'description',
                'feature_image',
            ],
            'grand-children-category-sort' => [
                'created_at',
                'uuid',
                'image',
                'slug',
                'parent_uuid',
                'publish_status',
                'title',
                'keyword',
                'description',
                'feature_image',
            ],
            'article-sort-order' => [
                'asc',
                'desc'
            ],
            'category-sort-order' => [
                'asc',
                'desc'
            ],
            'children-category-sort-order' => [
                'asc',
                'desc'
            ],
            'grand-children-category-sort-order' => [
                'asc',
                'desc'
            ]
        ];

        //create parent first
        foreach ($parentShortCodes as $parentShortCode) {
            WebsitePageShortCode::updateOrCreate(['key' => $parentShortCode['key']], $parentShortCode);
        }

        //create sort (order by)
        foreach ($sorts as $key => $sort) {
            if ($key == 'article_category') {
                foreach ($sort as $articleCategorySort) {
                    $parentSort = WebsitePageShortCode::where(['key' => 'article_category.sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleCategorySort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $articleCategorySort));
                }
            } elseif ($key == 'home_articles') {
                foreach ($sort as $homeArticlesSort) {
                    $parentSort = WebsitePageShortCode::where(['key' => 'home_articles.sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $homeArticlesSort['key']], array_merge(['parent_uuid' => $parentSort->uuid], $homeArticlesSort));
                }
            }
        }

        //create count (per page)
        foreach ($counts as $key => $count) {
            if ($key == 'article_category') {
                foreach ($count as $articleCategoryCount) {
                    $parentCount = WebsitePageShortCode::where(['key' => 'article_category.count'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleCategoryCount['key']], array_merge(['parent_uuid' => $parentCount->uuid], $articleCategoryCount));
                }
            } elseif ($key == 'home_articles') {
                foreach ($count as $homeArticlesCount) {
                    $parentCount = WebsitePageShortCode::where(['key' => 'home_articles.count'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $homeArticlesCount['key']], array_merge(['parent_uuid' => $parentCount->uuid], $homeArticlesCount));
                }
            }
        }

        //create short code
        foreach ($shortCodes['article_category'] as $key => $articleCategoryShortCode) {
            if ($key == 'article') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.article'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'category') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'children_category') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'grand_children_category') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.grand_children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'article-sort') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.article_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'category-sort') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'children-category-sort') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'grand-children-category-sort') {
                foreach ($articleCategoryShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'article_category.grand_children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            }
        }

        foreach ($shortCodes['article_detail'] as $articleDetailShortCode) {
            WebsitePageShortCode::updateOrCreate(['key' => $articleDetailShortCode['key']], $articleDetailShortCode);
        }

        foreach ($shortCodes['home_articles'] as $key => $homeArticleShortCode) {
            if ($key == 'article') {
                foreach ($homeArticleShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.article'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'category') {
                foreach ($homeArticleShortCode as $categoryShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $categoryShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $categoryShortCode));
                }
            } elseif ($key == 'children_category') {
                foreach ($homeArticleShortCode as $childrenCategoryShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.children_category'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $childrenCategoryShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $childrenCategoryShortCode));
                }
            } elseif ($key == 'article-sort') {
                foreach ($homeArticleShortCode as $homeArticleSort) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.article_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $homeArticleSort['key']], array_merge(['parent_uuid' => $parent->uuid], $homeArticleSort));
                }
            } elseif ($key == 'category-sort') {
                foreach ($homeArticleShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            } elseif ($key == 'children-category-sort') {
                foreach ($homeArticleShortCode as $articleShortCode) {
                    $parent = WebsitePageShortCode::where(['key' => 'home_articles.children_category_sort'])->first();
                    WebsitePageShortCode::updateOrCreate(['key' => $articleShortCode['key']], array_merge(['parent_uuid' => $parent->uuid], $articleShortCode));
                }
            }
        }
    }
}
