<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'code' => 'en',
                'flag_image' => 'GB',
            ],
            [
                'code' => 'vi',
                'flag_image' => 'VN',
            ],
            [
                'code' => 'de',
                'flag_image' => 'DE',
            ],
            [
                'code' => 'es',
                'flag_image' => 'ES',
            ],
            [
                'code' => 'sv',
                'flag_image' => 'SE',
            ],
            [
                'code' => 'ja',
                'flag_image' => 'JP',
            ],
            [
                'code' => 'ko',
                'flag_image' => 'KR',
            ],
            [
                'code' => 'zh',
                'flag_image' => 'CN',
            ],
            [
                'code' => 'ru',
                'flag_image' => 'RU',
            ],
            [
                'code' => 'ar',
                'flag_image' => 'SA',
            ],
        ];

        foreach ($languages as $language) {
            $jsonFe = File::exists(public_path("translate_json/{$language['code']}.json"))
                ? File::get(public_path("translate_json/{$language['code']}.json"))
                : File::get(public_path("translate_json/en.json"));

            if ($lang = Language::find($language['code'])) {
                $lang->update([
                    'fe' => $jsonFe
                ]);
            } else {
                Language::create([
                    'code' => $language['code'],
                    'name' => config('languages.codes')[$language['code']],
                    'flag_image' => $language['flag_image'],
                    'fe' => $jsonFe
                ]);
            }
        }
    }
}
