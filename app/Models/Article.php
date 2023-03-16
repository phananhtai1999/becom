<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}
