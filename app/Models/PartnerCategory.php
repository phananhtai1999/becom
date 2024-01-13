<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserProfileService;
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
        'content',
        'image'
    ];

    public $translatable = ['title','content'];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'title' => 'array',
        'content' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
        'contents'
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
    public function getTitlesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    /**
     * @return array|mixed
     */
    public function getContentsAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('content') : $this->content;
    }
}
