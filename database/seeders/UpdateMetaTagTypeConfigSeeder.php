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
                'en' => 'Document'
            ],
            'distributions' => [
                'en' => 'Global'
            ],
            'revisit-afters' => [
                'en' => '1 days'
            ],
            'GENERATORS' => [
                'en' => 'TechupZone Email Marketing'
            ],
            'fb:pages' => ''
        ];
        $config = Config::where('type', Config::CONFIG_META_TAG_TYPE)->first();
        if ($config) {
            $config->update([
                'value' => array_merge($config->value, $value)
            ]);
        }
    }
}
