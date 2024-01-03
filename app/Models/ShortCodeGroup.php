<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortCodeGroup extends Model
{
    use HasFactory, SoftDeletes;
    const ARTICLE_DETAIL = 'article_detail';
    const ARTICLE_CATEGORY = 'article_category';
    const HOME_ARTICLES = 'home_articles';

    const PRODUCT_DETAIL = 'product_detail';
    const PRODUCT_CATEGORY = 'product_category';
    const HOME_PRODUCTS = 'home_products';
    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function shortCodes()
    {
        return $this->belongsToMany(WebsitePageShortCode::class, 'short_code_short_code_group', 'short_code_group_uuid', 'short_code_uuid')->withTimestamps();
    }
}
