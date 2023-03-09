<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $configs = [
            [
                'key' => 'smtp_auto',
                'value' => false,
                'group_id' => 1
            ],
            [
                'key' => 'email_price',
                'value' => 1,
                'group_id' => 1
            ],
            [
                'key' => 'sms_price',
                'value' => 5,
                'group_id' => 1
            ],
            [
                "key" => "footer_linkedin",
                "value" => "https://www.linkedin.com",
                'group_id' => 1
            ],
            [
                "key" => "footer_twitter",
                "value" => "https://www.twitter.com",
                'group_id' => 1
            ],
            [
                "key" => "footer_instagram",
                "value" => "https://www.instagram.com",
                'group_id' => 1
            ],
            [
                "key" => "footer_facebook",
                "value" => "https://www.facebook.com",
                'group_id' => 1
            ],
            [
                "key" => "site_name",
                "value" => "Mail",
                'group_id' => 1
            ],
            [
                "key" => "logo",
                "value" => "https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png",
                'group_id' => 1
            ],
            [
                "key" => "favicon_icon",
                "value" => "https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png",
                'group_id' => 1

            ],
            [
                'key' => 'telegram_price',
                'value' => 1,
                'group_id' => 1
            ],
            [
                'key' => 'viber_price',
                'value' => 1,
                'group_id' => 1
            ],
        ];

        foreach ($configs as $config) {
            Config::firstOrCreate(
                ["key" => $config['key']],
                [
                    "value" => $config['value'],
                    "group_id" => $config['group_id']
                ]
            );
        }
    }
}
