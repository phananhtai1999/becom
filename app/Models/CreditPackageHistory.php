<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPackageHistory extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "credit_package_histories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_uuid',
        'credit_package_uuid',
        'payment_method_uuid',
        'logs',
        'invoice_uuid'
    ];

    public function creditPackage() {
        return $this->belongsTo(CreditPackage::class, 'credit_package_uuid', 'uuid');
    }
    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_uuid', 'uuid');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
