<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ActivityHistory extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "activity_histories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'type_id',
        'content',
        'date',
        'contact_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'datetime',
        'content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $fromCreatedAt
     * @return Builder
     */
    public function scopeFromCreatedAt(Builder $query, $fromCreatedAt): Builder
    {
        return $query->whereDate('activity_histories.created_at', '>=', $fromCreatedAt);
    }

    /**
     * @param Builder $query
     * @param $toCreatedAt
     * @return Builder
     */
    public function scopeToCreatedAt(Builder $query, $toCreatedAt): Builder
    {
        return $query->whereDate('activity_histories.created_at', '<=', $toCreatedAt);
    }

    /**
     * @param Builder $query
     * @param $fromDate
     * @return Builder
     */
    public function scopeFromDate(Builder $query, $fromDate): Builder
    {
        return $query->whereDate('date', '>=', $fromDate);
    }

    /**
     * @param Builder $query
     * @param $toDate
     * @return Builder
     */
    public function scopeToDate(Builder $query, $toDate): Builder
    {
        return $query->whereDate('date', '<=', $toDate);
    }
}
