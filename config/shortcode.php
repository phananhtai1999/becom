<?php
return [
    'category' => [
        'title' => 'category.title',
        'content' => 'category.content',
        'image' => 'category.image',
        'feature_image' => 'category.feature_image',
        'keyword' => 'category.keyword',
        'description' => 'category.description',
        'short_content' => 'category.short_content'
    ],
    'home_article' => [
        'article.title',
        'article.content',
        'article.image',
        'article.video',
        'article.keyword',
        'article.description',
        'article.short_content'
    ],
    'children_category' => [
        'children_category.title',
        'children_category.content',
        'children_category.image',
        'children_category.feature_image',
        'children_category.keyword',
        'children_category.description',
        'children_category.short_content'
    ],
    'grand_children_category' => [
        'grand_children_category.title',
        'grand_children_category.content',
        'grand_children_category.image',
        'grand_children_category.feature_image',
        'grand_children_category.keyword',
        'grand_children_category.description',
        'grand_children_category.short_content',
    ],
    'element' => [
        'category',
        'grand_children_category',
        'children_category',
        'article',
    ],
    'element_count' => [
        'data-grand-children-category-count',
        'data-children-category-count',
        'data-article-count',
        'data-category-count',
    ],
    'article-sort' => [
        'uuid',
        'created_at',
        'image',
        'slug',
        'publish_status',
        'title',
        'content',
        'video',
        'content_for_user',
        'reject_reason',
        'content_type',
        'single_purpose_uuid',
        'paragraph_type_uuid',
        'keyword',
        'description',
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
    ],
];
