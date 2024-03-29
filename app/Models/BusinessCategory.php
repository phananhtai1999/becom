<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserProfileService;
use App\Services\UserService;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BusinessCategory extends AbstractModel
{
    use HasFactory, WorksAsNestedSet,
        SoftDeletes, HasTranslations,
        ModelFilterLanguageTrait;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;


    /**
     * @var string
     */
    protected $table = "business_categories";

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
        'parent_uuid',
        'publish_status',
        'title'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function mailTemplates()
    {
        return $this->hasMany(MailTemplate::class, 'business_category_uuid', 'uuid');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_business_category', 'business_category_uuid', 'contact_uuid')->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function parentBusinessCategory()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenBusinessCategory()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenBusinessCategoryPublic()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid')->where('publish_status', true);
    }

    /**
     * @param Builder $query
     * @param $data
     * @return Builder|void
     */
    public function scopeCategoryRoot(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('parent_uuid');
        }
    }

    /**
     * @return array|mixed
     */
    public function getTitlesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businessManagements()
    {
        return $this->belongsToMany(BusinessManagement::class, 'management_categories', 'category_uuid', 'management_uuid')->withTimestamps();
    }
}
