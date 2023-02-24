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
            ],
            [
                'key' => 'email_price',
                'value' => 1,
            ],
            [
                'key' => 'sms_price',
                'value' => 5,
            ],
            [
                "key"=> "footer_linkedin",
                "value"=> "https://www.linkedin.com",
            ],
            [
                "key"=> "footer_twitter",
                "value"=> "https://www.twitter.com",
            ],
            [
                "key"=> "footer_instagram",
                "value"=> "https://www.instagram.com",
            ],
            [
                "key"=> "footer_facebook",
                "value"=> "https://www.facebook.com",
            ],
            [
                "key"=> "site_name",
                "value"=> "Mail",
            ],
            [
                "key"=> "logo",
                "value"=> "https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png",
            ]

        ];

        foreach ($configs as $config) {
            Config::firstOrCreate(
                ["key" => $config['key']],
                ["value" => $config['value']]
            );
        }
    }
}
