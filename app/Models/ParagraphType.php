<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactTitleCategoryLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserProfileService;
use App\Services\UserService;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ParagraphType extends AbstractModel
{
    use HasFactory, WorksAsNestedSet,
        SoftDeletes, HasTranslations,
        ModelFilterLanguageTrait, ModelFilterExactTitleCategoryLanguageTrait;

    /**
     * @var string
     */
    protected $table = "paragraph_types";

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

    /**
     * @var string[]
     */
    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'slug',
        'parent_uuid',
        'user_uuid',
        'title',
        'sort',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sort' => 'integer',
        'parent_uuid' => 'integer',
        'title' => 'array'
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
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
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
     * @return mixed
     */
    public function getTitlesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    /**
     * @return BelongsTo
     */
    public function parentParagraphType()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childrenParagraphType()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid')->orderBy('sort');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sortDescChildrenParagraphType()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid')->orderByDesc('sort');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'paragraph_type_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeParentParagraphTypeTitle(Builder $query, ...$titles)
    {
        return $query->where('parent_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('paragraph_types as a')
                ->whereColumn('a.uuid', 'paragraph_types.parent_uuid')
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
