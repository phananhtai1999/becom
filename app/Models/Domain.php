<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "domains";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'verified_at',
        'business_uuid',
        'owner_uuid',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sendProjects()
    {
        return $this->hasMany(SendProject::class, 'domain_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessManagement()
    {
        return $this->belongsTo(BusinessManagement::class, 'business_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function domainVerification()
    {
        return $this->hasOne(DomainVerification::class, 'domain_uuid', 'uuid');
    }
}
