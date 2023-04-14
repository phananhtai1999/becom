<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteVerification extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "website_verifications";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'send_project_uuid',
        'token',
        'verified_at'
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
     * @var string[]
     */
    protected $appends = ['was_verified'];

    /**
     * @return bool
     */
    public function getWasVerifiedAttribute()
    {
        return !empty($this->verified_at);
    }

    /**
     * @return BelongsTo
     */
    public function sendProject()
    {
        return $this->belongsTo(SendProject::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $verifiedAt
     * @return Builder
     */
    public function scopeFromVerifiedAt(Builder $query, $verifiedAt): Builder
    {
        return $query->whereDate('verified_at', '>=', $verifiedAt);
    }

    /**
     * @param Builder $query
     * @param $verifiedAt
     * @return Builder
     */
    public function scopeToVerifiedAt(Builder $query, $verifiedAt): Builder
    {
        return $query->whereDate('verified_at', '<=', $verifiedAt);
    }
}
