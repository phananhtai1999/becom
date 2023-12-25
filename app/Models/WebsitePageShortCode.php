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
        'status',
        'key',
        'parent_uuids',
        'name',
        'short_code',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'parent_uuids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    public function parentWebsitePageShortCode()
    {
        return WebsitePageShortCode::whereIn('uuid', $this->parent_uuids ?? [])->where('status', true)->get();
    }

    public function childrenWebsitePageShortCode()
    {
        return WebsitePageShortCode::whereJsonContains('parent_uuids', $this->uuid)->where('status', true)->get();
    }

    public function scopeShortCodeRoot(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('parent_uuids');
        }
    }

    public function scopeParentShortCode(Builder $query, $parent)
    {
        if ($parent) {
            return $query->whereJsonContains('parent_uuids', $parent);
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
