<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPaymentByDay extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "user_payment_by_day";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid', 'payment', 'month', 'year', 'total_payment', 'created_at', 'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_uuid' => 'integer',
        'total_payment' => 'integer',
        'month' => 'integer',
        'year' => 'integer',
        'payment' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
