<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
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
        ModelFilterLanguageTrait;

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
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' => 'integer',
        'sort' => 'integer',
        'parent_uuid' => 'integer',
        'title' => 'array'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'title_translate',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
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
    public function getTitleTranSlateAttribute()
    {
        return $this->title;
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
}
