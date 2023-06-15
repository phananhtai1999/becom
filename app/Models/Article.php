<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\ArticleCategoryService;
use App\Services\UserService;
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
    const BLOCKED_PUBLISH_STATUS = 2;
    const PENDING_PUBLISH_STATUS = 3;
    const REJECT_PUBLISH_STATUS = 4;
    const PUBLIC_CONTENT_FOR_USER = 'public';
    const LOGIN_CONTENT_FOR_USER = 'login';
    const EDITOR_CONTENT_FOR_USER = 'editor';
    const PAYMENT_CONTENT_FOR_USER = 'payment';
    const ADMIN_CONTENT_FOR_USER = 'admin';

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
        'content',
        'video',
        'content_for_user',
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
        'short_content',
        'title_translate',
        'content_translate',
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

    /**
     * @return array|mixed
     */
    public function getContentTranslateAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('content') : $this->content;
    }

    /**
     * @return array|mixed
     */
    public function getTitleTranslateAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    public function scopeArticleCategoryTitle(Builder $query, ...$titles)
    {
        return $query->where('article_category_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('article_categories as a')
                ->whereColumn('a.uuid', 'articles.article_category_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }

    public function scopeTitleByRoot(Builder $query, ...$search)
    {
        $rootUuid = $search[0];
        $title = $search[1] ?? null;
        $lang = app()->getLocale();
        $langDefault = config('app.fallback_locale');
        $articleCategoryUuids = (new ArticleCategoryService())->getArticleCategoriesByRootUuid($rootUuid)->pluck('uuid');

        return $query->whereIn('article_category_uuid', $articleCategoryUuids)
            ->whereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(title, '$.$langDefault'))) like '%$title%'");
    }
}
