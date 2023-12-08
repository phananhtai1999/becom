<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterDescriptionLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterKeywordLanguageTrait;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class WebsitePage extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations,
        ModelFilterDescriptionLanguageTrait, ModelFilterKeywordLanguageTrait, HasSlug;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;
    const DRAFT_PUBLISH_STATUS = 4;

    const STATIC_TYPE = 'static';
    const ARTICLE_DETAIL_TYPE = 'news.article_detail';
    const ARTICLE_CATEGORY_TYPE = 'news.article_category';
    const HOME_ARTICLES_TYPE = 'news.home_articles';

    /**
     * @var string
     */
    protected $table = "website_pages";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    public $translatable = ['keyword', 'description'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'template',
        'template_json',
        'user_uuid',
        'type',
        'publish_status',
        'is_default',
        'website_page_category_uuid',
        'display_type',
        'reject_reason',
        'keyword',
        'description',
        'feature_image',
        'slug',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'template_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' => 'integer',
        'website_page_category_uuid' => 'integer',
        'is_default' => 'boolean',
        'reject_reason' => 'array',
        'keyword' => 'array',
        'description' => 'array',
    ];

    protected $appends = [
        'keywords',
        'descriptions',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100)
            ->usingSeparator('-')
            ->allowDuplicateSlugs()
            ->doNotGenerateSlugsOnUpdate();
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
    public function websitePageCategory()
    {
        return $this->belongsTo(WebsitePageCategory::class, 'website_page_category_uuid', 'uuid',);
    }

    public function websites()
    {
        return $this->belongsToMany(Website::class, 'website_website_page', 'website_page_uuid', 'website_uuid')->orderBy('created_at')->withPivot(['is_homepage', 'ordering'])->withTimestamps()->first();
    }

    public function getKeywordsAttribute()
    {
        return $this->keyword ? $this->getTranslations('keyword') : $this->keyword;
    }

    public function getDescriptionsAttribute()
    {
        return $this->description ? $this->getTranslations('description') : $this->description;
    }
}
