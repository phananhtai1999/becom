<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutInformation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "payout_informations";

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
        'currency'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
