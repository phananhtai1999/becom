<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\PartnerLevelService;
use App\Services\PartnerUserService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class PartnerTrackingByYear extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "partner_tracking_by_year";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'partner_uuid', 'commission', 'total_commission', 'year'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'partner_uuid' => 'integer',
        'commission' => 'array',
        'total_commission' => 'integer',
        'year' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }
}
