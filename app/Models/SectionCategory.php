<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactTitleCategoryLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterTitleCategoryLanguageTrait;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class SectionCategory extends AbstractModel
{
    use HasFactory, SoftDeletes,
        HasTranslations, ModelFilterLanguageTrait, ModelFilterTitleCategoryLanguageTrait,
        ModelFilterExactTitleCategoryLanguageTrait;

    /**
     * @var string
     */
    protected $table = "section_categories";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
    ];

    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'title' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
    ];

    /**
     * @return HasMany
     */
    public function sectionTemplates()
    {
        return $this->hasMany(SectionTemplate::class, 'section_category_uuid', 'uuid');
    }

    /**
     * @return array|mixed
     */
    public function getTitlesAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }
}
