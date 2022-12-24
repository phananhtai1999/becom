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
        $key = [
            [
                'key' => 'smtp_auto',
                'value' => true,
            ],
            [
                'key' => 'email_price',
                'value' => 1,
            ],
            [
                'key' => 'sms_price',
                'value' => 5,
            ],
        ];

        foreach ($key as $value) {
            Config::firstOrCreate($value);
        }
    }
}
