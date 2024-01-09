<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "companies";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_uuid',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'company_contact', 'company_uuid', 'contact_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }
}
