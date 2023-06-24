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
        'payout_information_uuid'
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
        return $this->belongsTo(User::class, 'by_user_uuid', 'uuid');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }

    public function payoutInformation()
    {
        return $this->belongsTo(PayoutInformation::class, 'payout_information_uuid', 'uuid');
    }
}
