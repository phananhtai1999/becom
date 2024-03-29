<?php

namespace Database\Seeders;

use App\Models\ShortCodeGroup;
use App\Models\WebsitePage;
use Illuminate\Database\Seeder;

class ShortCodeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shortCodeGroups = [
            [
                'key' => ShortCodeGroup::HOME_ARTICLES,
                'name' => 'Home page group',
                'description' => 'This is group include short code in page home'
            ],
            [
                'key' => ShortCodeGroup::ARTICLE_DETAIL,
                'name' => 'Article detail page group',
                'description' => 'This is group include short code in page article detail'
            ],
            [
                'key' => ShortCodeGroup::ARTICLE_CATEGORY,
                'name' => 'Article category page group',
                'description' => 'This is group include short code in page article category'
            ],
            [
                'key' => ShortCodeGroup::HOME_PRODUCTS,
                'name' => 'Home page group',
                'description' => 'This is group include short code in page home'
            ],
            [
                'key' => ShortCodeGroup::PRODUCT_DETAIL,
                'name' => 'product detail page group',
                'description' => 'This is group include short code in page product detail'
            ],
            [
                'key' => ShortCodeGroup::PRODUCT_CATEGORY,
                'name' => 'product category page group',
                'description' => 'This is group include short code in page product category'
            ],
        ];

        foreach ($shortCodeGroups as $shortCodeGroup) {
            ShortCodeGroup::updateOrCreate(['key' => $shortCodeGroup['key']], $shortCodeGroup);
        }
    }
}
