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
        $squareGroupId = AssetGroup::where('code', 'square_and_rectangle')->first()->uuid();
        $skyscraperGroupId = AssetGroup::where('code', 'skyscraper')->first()->uuid();
        $leaderboardGroupId = AssetGroup::where('code', 'leaderboard')->first()->uuid();
        $mobileGroupId = AssetGroup::where('code', 'mobile')->first()->uuid();
        $assetSizes = [
            [
                'name' => 'Small square',
                'width' => '200',
                'height' => '200',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Vertical rectangle',
                'width' => '200',
                'height' => '400',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Square',
                'width' => '250',
                'height' => '250',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Triple widescreen',
                'width' => '250',
                'height' => '360',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Inline rectangle',
                'width' => '300',
                'height' => '250',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Large rectangle',
                'width' => '336',
                'height' => '280',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Netboard',
                'width' => '580',
                'height' => '400',
                'asset_group_uuid' => $squareGroupId
            ],
            [
                'name' => 'Skyscraper',
                'width' => '120',
                'height' => '600',
                'asset_group_uuid' => $skyscraperGroupId
            ],
            [
                'name' => 'Wide skyscraper',
                'width' => '120',
                'height' => '600',
                'asset_group_uuid' => $skyscraperGroupId
            ],
            [
                'name' => 'Half-page ad',
                'width' => '300',
                'height' => '600',
                'asset_group_uuid' => $skyscraperGroupId
            ],
            [
                'name' => 'Portrait',
                'width' => '300',
                'height' => '1050',
                'asset_group_uuid' => $skyscraperGroupId
            ],
            [
                'name' => 'Banner',
                'width' => '468',
                'height' => '60',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Leaderboard',
                'width' => '729',
                'height' => '90',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Top banner',
                'width' => '930',
                'height' => '180',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Large leaderboard',
                'width' => '970',
                'height' => '90',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Billboard',
                'width' => '970',
                'height' => '250',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Panorama',
                'width' => '980',
                'height' => '120',
                'asset_group_uuid' => $leaderboardGroupId
            ],
            [
                'name' => 'Mobile banner',
                'width' => '300',
                'height' => '50',
                'asset_group_uuid' => $mobileGroupId
            ],
            [
                'name' => 'Mobile banner',
                'width' => '320',
                'height' => '50',
                'asset_group_uuid' => $mobileGroupId
            ],
            [
                'name' => 'Large mobile banner',
                'width' => '300',
                'height' => '100',
                'asset_group_uuid' => $mobileGroupId
            ]
        ];

        foreach ($assetSizes as $assetSize) {
            AssetSize::firstOrCreate([
                "name" => $assetSize['name'],
                "width" => $assetSize['width'],
                "height" => $assetSize['height'],
                'asset_group_uuid' => $assetSize['asset_group_uuid']
            ],
            );
        }
    }
}
