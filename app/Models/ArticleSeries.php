<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactTitleCategoryLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserService;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ArticleSeries extends AbstractModel
{
    use HasFactory, WorksAsNestedSet,
        SoftDeletes, HasTranslations,
        ModelFilterLanguageTrait, ModelFilterExactTitleCategoryLanguageTrait;

    /**
     * @var string
     */
    protected $table = "article_series";

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

    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'slug',
        'parent_uuid',
        'title',
        'article_category_uuid',
        'assigned_ids',
        'list_keywords',
        'article_uuid',
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
        'article_category_uuid' => 'integer',
        'assigned_ids' => 'integer',
        'article_uuid' => 'integer',
        'title' => 'array',
        'list_keywords' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
    ];

    /**
     * @return BelongsTo
     */
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function parentArticleSeries()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenArticleSeries()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
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
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeExactParentArticleSeriesTitle(Builder $query, ...$titles)
    {
        return $query->where('parent_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('article_series as a')
                ->whereColumn('a.uuid', 'article_series.parent_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }

    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeParentArticleSeriesTitle(Builder $query, ...$titles)
    {
        return $query->where('parent_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('article_series as a')
                ->whereColumn('a.uuid', 'article_series.parent_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) like '%$title%'");
                    }
                });
        });
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_ids', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_uuid', 'uuid');
    }
}
