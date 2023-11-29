<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsitePageShortCode extends Model
{
    use HasFactory, SoftDeletes;

    const ARTICLE_DETAIL_TYPE = 'article_detail';
    const ARTICLE_CATEGORY_TYPE = 'article_category';
    const HOME_ARTICLES_TYPE = 'home_articles';
    /**
     * @var string
     */
    protected $table = "website_page_short_codes";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'type',
        'key',
        'parent_uuid',
        'name',
        'short_code',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function parentWebsitePageShortCode()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    public function childrenWebsitePageShortCode()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
    }

    public function scopeShortCodeRoot(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('parent_uuid');
        }
    }

    /**
     * @return BelongsToMany
     */
    public function shortCodeGroups()
    {
        return $this->belongsToMany(ShortCodeGroup::class, 'short_code_short_code_group', 'short_code_uuid', 'short_code_group_uuid')->withTimestamps();
    }
}
