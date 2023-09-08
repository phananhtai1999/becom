<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class UpdateMetaTagTypeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $value = [
            'copyrights' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'authors' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'resource-types' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'distributions' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'revisit-afters' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'GENERATORS' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'fb:pages' => 'TechupZone Email Marketing'
        ];
        $config = Config::where('type', Config::CONFIG_META_TAG_TYPE)->first();
        if ($config) {
            $config->update([
                'value' => array_merge($config->value, $value)
            ]);
        }
    }
}
