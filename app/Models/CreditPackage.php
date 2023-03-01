<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPackage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "credit_packages";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'price',
        'credit'
    ];
}
