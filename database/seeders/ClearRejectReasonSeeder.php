<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Asset;
use App\Models\Form;
use App\Models\MailTemplate;
use App\Models\SectionTemplate;
use App\Models\WebsitePage;
use Illuminate\Database\Seeder;

class ClearRejectReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WebsitePage::query()->update(['reject_reason' => null]);
        MailTemplate::query()->update(['reject_reason' => null]);
        Form::query()->update(['reject_reason' => null]);
        SectionTemplate::query()->update(['reject_reason' => null]);
        Article::query()->update(['reject_reason' => null]);
        Asset::query()->update(['reject_reason' => null]);
    }
}
