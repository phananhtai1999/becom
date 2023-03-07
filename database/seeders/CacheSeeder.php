<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class CacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::create([
                'key' => 'starter_cache',
                'group_id' => '1',
                'value' => [
                    '001A' => 'Unlimited contacts',
                    '002A' => '499 emails/day',
                    '003A' => 'Drag & Drop Editor',
                ]
            ]);
        Config::create([
            'key' => 'professional_cache',
            'group_id' => '1',
            'value' => [
                '001B' => 'No daily sending limit',
                '002B' => 'No Mail logo (add-on)',
                '003B' => 'Sending optimization',
            ]
        ]);
        Config::create([
            'key' => 'business_cache',
            'group_id' => '1',
            'value' => [
                '001C' => 'Telegram Campaigns',
                '002C' => 'Viber Campaigns',
                '003C' => 'Fast sending optimization',
            ]
        ]);
    }
}
