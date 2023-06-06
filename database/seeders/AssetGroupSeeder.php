<?php

namespace Database\Seeders;

use App\Models\AssetGroup;
use Illuminate\Database\Seeder;

class AssetGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assetGroups = [
            [
                'name' => 'Square and rectangle',
                'code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Skyscraper',
                'code' => 'skyscraper'
            ],
            [
                'name' => 'Leaderboard',
                'code' => 'leaderboard'
            ],
            [
                'name' => 'Mobile',
                'code' => 'mobile'
            ]
        ];

        foreach ($assetGroups as $assetGroup) {
            AssetGroup::firstOrCreate(
                ["name" => $assetGroup['name'], 'code' => $assetGroup['code']],
            );
        }
    }
}
