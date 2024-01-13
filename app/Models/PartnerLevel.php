<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserProfileService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PartnerLevel extends AbstractModel
{
    use HasFactory, HasTranslations, ModelFilterLanguageTrait;

    /**
     * @var string
     */
    protected $table = "partner_levels";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'number_of_customers',
        'commission',
        'content',
        'image'
    ];

    /**
     * @var string[]
     */
    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'number_of_customers' => 'integer',
        'commission' => 'integer'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
        'contents'
    ];

    /**
     * @return mixed
     */
    public function getTitlesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    /**
     * @return mixed
     */
    public function getContentsAttribute()
    {
        $language = app()->getLocale();
        $langDefault = config('app.fallback_locale');
        $contents = $this->content;
        if ($contents) {
            $result = [];
            foreach ($contents as $content) {
                foreach ($content as $lang => $value) {
                    $result[$lang][] = $value;
                }
            }
            return array_key_exists($language, $result) ? $result[$language] : $result[$langDefault];
        }

        return null;

    }
}
