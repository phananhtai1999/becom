<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Techup\ApiConfig\Models\Config;

class BankInformation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "bank_informations";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'swift_code',
        'bank_name',
        'bank_address',
        'is_verified',
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

    /**
     * @var string[]
     */
    protected $appends = [
        'payout_fee',
    ];

    /**
     * @return string
     */
    public function getPayoutFeeAttribute()
    {
        return optional(Config::where('key', 'payout_fee')->first())->value;
    }

}
