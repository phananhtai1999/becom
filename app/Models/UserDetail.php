<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "user_details";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'about',
        'gender',
        'date_of_birth',
        'user_uuid',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $dateOfBirth
     * @return Builder
     */
    public function scopeFromDateOfBirth(Builder $query, $dateOfBirth): Builder
    {
        return $query->whereDate('date_of_birth', '>=', $dateOfBirth);
    }

    /**
     * @param Builder $query
     * @param $dateOfBirth
     * @return Builder
     */
    public function scopeDateOfBirth(Builder $query, $dateOfBirth): Builder
    {
        return $query->whereDate('date_of_birth', '<=', $dateOfBirth);
    }
}
