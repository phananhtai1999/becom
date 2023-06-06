<?php

namespace Database\Seeders;

use App\Models\AssetGroup;
use App\Models\AssetSize;
use Illuminate\Database\Seeder;

class AssetSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $assetSizes = [
            [
                'name' => 'Small square',
                'width' => '200',
                'height' => '200',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Vertical rectangle',
                'width' => '200',
                'height' => '400',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Square',
                'width' => '250',
                'height' => '250',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Triple widescreen',
                'width' => '250',
                'height' => '360',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Inline rectangle',
                'width' => '300',
                'height' => '250',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Large rectangle',
                'width' => '336',
                'height' => '280',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Netboard',
                'width' => '580',
                'height' => '400',
                'asset_group_code' => 'square_and_rectangle'
            ],
            [
                'name' => 'Skyscraper',
                'width' => '120',
                'height' => '600',
                'asset_group_code' => 'skyscraper'
            ],
            [
                'name' => 'Wide skyscraper',
                'width' => '120',
                'height' => '600',
                'asset_group_code' => 'skyscraper'
            ],
            [
                'name' => 'Half-page ad',
                'width' => '300',
                'height' => '600',
                'asset_group_code' => 'skyscraper'
            ],
            [
                'name' => 'Portrait',
                'width' => '300',
                'height' => '1050',
                'asset_group_code' => 'skyscraper'
            ],
            [
                'name' => 'Banner',
                'width' => '468',
                'height' => '60',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Leaderboard',
                'width' => '729',
                'height' => '90',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Top banner',
                'width' => '930',
                'height' => '180',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Large leaderboard',
                'width' => '970',
                'height' => '90',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Billboard',
                'width' => '970',
                'height' => '250',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Panorama',
                'width' => '980',
                'height' => '120',
                'asset_group_code' => 'leaderboard'
            ],
            [
                'name' => 'Mobile banner',
                'width' => '300',
                'height' => '50',
                'asset_group_code' => 'mobile'
            ],
            [
                'name' => 'Mobile banner',
                'width' => '320',
                'height' => '50',
                'asset_group_code' => 'mobile'
            ],
            [
                'name' => 'Large mobile banner',
                'width' => '300',
                'height' => '100',
                'asset_group_code' => 'mobile'
            ]
        ];

        foreach ($assetSizes as $assetSize) {
            AssetSize::firstOrCreate([
                "name" => $assetSize['name'],
                "width" => $assetSize['width'],
                "height" => $assetSize['height'],
                'asset_group_code' => $assetSize['asset_group_code']
            ],
            );
        }
    }
}
