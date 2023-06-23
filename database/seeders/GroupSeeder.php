<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
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
                'name' => 'general',
            ],
            [
                'name' => 'otp',
            ],
            [
                'name' => 'payment',
            ],
            [
                'name' => 'company',
            ],
            [
                'name' => 's3',
            ],
            [
                'name' => 'site',
            ],
            [
                'name' => 'asset',
            ],
            [
                'name' => 'payout',
            ]
        ];

        foreach ($configs as $config) {
            Group::firstOrCreate(
                ["name" => $config['name']],
            );
        }
    }
}
