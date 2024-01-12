<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterDescriptionLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterKeywordLanguageTrait;
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
        HasTranslations, ModelFilterLanguageTrait, ModelFilterKeywordLanguageTrait,
        ModelFilterDescriptionLanguageTrait;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const BLOCKED_PUBLISH_STATUS = 2;
    const PENDING_PUBLISH_STATUS = 3;
    const REJECT_PUBLISH_STATUS = 4;
    const DRAFT_PUBLISH_STATUS = 5;
    const PUBLIC_CONTENT_FOR_USER = 'public';
    const LOGIN_CONTENT_FOR_USER = 'login';
    const EDITOR_CONTENT_FOR_USER = 'editor';
    const PAYMENT_CONTENT_FOR_USER = 'payment';
    const ADMIN_CONTENT_FOR_USER = 'admin';
    const PARAGRAPH_CONTENT_TYPE = 'paragraph';
    const SINGLE_CONTENT_TYPE = 'single';

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
        'keyword',
        'description',
        'video',
        'content_for_user',
        'reject_reason',
        'content_type',
        'single_purpose_uuid',
        'paragraph_type_uuid',
        'app_id',
    ];

    public $translatable = ['title', 'content', 'keyword', 'description'];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'title' => 'array',
        'content' => 'array',
        'keyword' => 'array',
        'description' => 'array',
        'article_category_uuid' => 'integer',
        'single_purpose_uuid' => 'integer',
        'paragraph_type_uuid' => 'integer',
        'reject_reason' => 'array',
    ];

    protected $appends = [
        'short_content',
        'titles',
        'contents',
        'keywords',
        'descriptions',
    ];

    /**
     * @return string
     */
    public function getShortContentAttribute()
    {
        if ($this->content_type === Article::PARAGRAPH_CONTENT_TYPE) {
            $result = '';
            $contents = json_decode($this->content, true);
            if ($contents && is_array($contents)) {
                foreach ($contents as $content) {
                    $result .= $content['content'] . ' ';
                }
                $result = trim($result);

                return Str::limit(html_entity_decode(htmlspecialchars_decode(strip_tags($result))), 500);
            }
        }

        return Str::limit(strip_tags($this->content), 500);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

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
    public function singlePurpose()
    {
        return $this->belongsTo(SinglePurpose::class, 'single_purpose_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function paragraphType()
    {
        return $this->belongsTo(ParagraphType::class, 'paragraph_type_uuid', 'uuid');
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
    public function getContentsAttribute()
    {
        return $this->getTranslations('content');
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function articleSerie()
    {
        return $this->hasOne(ArticleSeries::class, 'article_uuid', 'uuid');
    }
}
