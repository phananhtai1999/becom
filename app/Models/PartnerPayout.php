<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerPayout extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "partner_payouts";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'by_user_uuid',
        'partner_uuid',
        'amount',
        'time',
        'status',
        'payout_method_uuid',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'time' => 'datetime',
        'by_user_uuid' => 'integer',
        'partner_uuid' => 'integer',
        'amount' => 'float'
    ];

    /**
     * @return BelongsTo
     */
    public function byUser()
    {
        return $this->belongsTo(UserProfile::class, 'by_user_uuid', 'user_uuid');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }

    public function payoutMethod()
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_uuid', 'uuid');
    }
}
