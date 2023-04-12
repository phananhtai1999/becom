<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Partner extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "partners";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'partner_email',
        'user_uuid',
        'publish_status',
        'phone_number',
        'answer',
        'partner_category_uuid',
        'partner_level_uuid',
        'code'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'partner_category_uuid' => 'integer',
        'user_uuid' => 'integer',
        'partner_level_uuid' => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return ($this->first_name) . ' ' . ($this->last_name);
    }

    /**
     * @return BelongsTo
     */
    public function partnerCategory()
    {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function partnerTrackings()
    {
        return $this->hasMany(PartnerTracking::class, 'partner_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function partnerLevel()
    {
        return $this->belongsTo(PartnerLevel::class, 'partner_level_uuid', 'uuid');
    }

    public function scopePartnerCategoryTitle(Builder $query, ...$titles)
    {
        return $query->where('partner_category_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('partner_categories as a')
                ->whereColumn('a.uuid', 'partners.partner_category_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }

    public function scopePartnerLevelTitle(Builder $query, ...$titles)
    {
        return $query->where('partner_level_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('partner_levels as a')
                ->whereColumn('a.uuid', 'partners.partner_level_uuid')
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
