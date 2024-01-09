<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutMethod extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "payout_methods";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'email',
        'account_number',
        'payout_fee',
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'phone',
        'name_on_account',
        'user_uuid',
        'is_default',
        'swift_code',
        'bank_name',
        'bank_address',
        'currency',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'last_4',
    ];

    /**
     * @return string
     */
    public function getLast4Attribute()
    {
        return substr($this->account_number, -4);
    }

    public function user() {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }
}
