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
        ];

        foreach ($key as $value) {
            Config::firstOrCreate($value);
        }
    }
}
