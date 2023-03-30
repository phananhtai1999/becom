<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    const ADD_ON_DRAFT = 'draft';
    const ADD_ON_PUBLISH = 'publish';
    const ADD_ON_DISABLE = 'disable';
    const ADD_ON_DURATION_MONTH = 'month';
    const ADD_ON_DURATION_YEAR = 'year';
    /**
     * @var string
     */
    protected $table = "add_ons";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'monthly',
        'yearly',
        'name',
        'description',
        'thumbnail',
        'status',
        'payment_product_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'payment_product_id' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'add_on_permissions', 'add_on_uuid', 'permission_uuid');
    }

    public function addOnSubscriptionPlans()
    {
        return $this->hasMany(AddOnSubscriptionPlan::class, 'add_on_uuid', 'uuid');
    }
}
