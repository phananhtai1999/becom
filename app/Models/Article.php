<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Article extends AbstractModel
{
    use HasFactory, SoftDeletes,
        HasTranslations, ModelFilterLanguageTrait;

    const PUBLISHED_PUBLISH_STATUS = 1;

    /**
     * @var string
     */
    protected $table = "articles";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'image',
        'slug',
        'user_uuid',
        'article_category_uuid',
        'publish_status',
        'title',
        'content'
    ];

    public $translatable = ['title', 'content'];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'title' => 'array',
        'content' => 'array',
        'user_uuid' => 'integer',
        'article_category_uuid' => 'integer',
    ];

    protected $appends = [
        'short_content'
    ];

    public function getShortContentAttribute()
    {
        return Str::limit(strip_tags($this->content), 500);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeFromCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeFromUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '>=', $date);
    }

    /**
     * @param Builder $query
     * @param $date
     * @return Builder
     */
    public function scopeToUpdatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('updated_at', '<=', $date);
    }
}
