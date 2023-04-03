<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PartnerCategory extends AbstractModel
{
    use HasFactory, SoftDeletes,
        HasTranslations, ModelFilterLanguageTrait;

    /**
     * @var string
     */
    protected $table = "partner_categories";

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
        'title_translate',
    ];

    /**
     * @return HasMany
     */
    public function partners()
    {
        return $this->hasMany(Partner::class, 'partner_category_uuid', 'uuid');
    }

    /**
     * @return array|mixed
     */
    public function getTitleTranslateAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }
}
