<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterDescriptionLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterKeywordLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserService;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ArticleCategory extends AbstractModel
{
    use HasFactory, WorksAsNestedSet,
        SoftDeletes, HasTranslations,
        ModelFilterLanguageTrait,
        ModelFilterKeywordLanguageTrait, ModelFilterDescriptionLanguageTrait;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;


    /**
     * @var string
     */
    protected $table = "article_categories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Column name which stores reference to parent's node.
     *
     * @var string
     */
    protected $parentColumnName = 'parent_uuid';

    /**
     * Column name for the left index.
     *
     * @var string
     */
    protected $leftColumnName = 'left';

    /**
     * Column name for the right index.
     *
     * @var string
     */
    protected $rightColumnName = 'right';

    /**
     * Column name for the depth field.
     *
     * @var string
     */
    protected $depthColumnName = 'depth';

    public $translatable = ['title', 'keyword', 'description'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'image',
        'slug',
        'parent_uuid',
        'user_uuid',
        'publish_status',
        'title',
        'keyword',
        'description',
        'feature_image',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'parent_uuid' => 'integer',
        'title' => 'array',
        'keyword' => 'array',
        'description' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
        'keywords',
        'descriptions',
    ];

    /**
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return BelongsTo
     */
    public function parentArticleCategory()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenArticleCategory()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenArticleCategoryPublic()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid')->where('publish_status', true);
    }

    /**
     * @return HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'article_category_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $check
     * @return Builder|void
     */
    public function scopeCategoryRoot(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('parent_uuid');
        }
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

    /**
     * @return array|mixed
     */
    public function getTitlesAttribute()
    {
        return $this->getTranslations('title');
    }

    public function getKeywordsAttribute()
    {
        return $this->getTranslations('keyword');
    }

    public function getDescriptionsAttribute()
    {
        return $this->getTranslations('description');
    }

    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeParentArticleCategoryTitle(Builder $query, ...$titles)
    {
        return $query->where('parent_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('article_categories as a')
                ->whereColumn('a.uuid', 'article_categories.parent_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }
}
