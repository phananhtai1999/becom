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

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;

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
        'work_email',
        'publish_status',
        'phone_number',
        'answer',
        'partner_category_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'publish_status' => 'integer',
        'partner_category_uuid' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function partnerCategory()
    {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_uuid', 'uuid');
    }

    public function scopePartnerCategoryTitle(Builder $query, $title)
    {
        return $query->where('partner_category_uuid', function ($q) use ($title) {
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            $q->select('a.uuid')
                ->from('partner_categories as a')
                ->whereColumn('a.uuid', 'partners.partner_category_uuid')
                ->whereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
        });
    }
}
