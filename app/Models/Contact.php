<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Contact extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "contacts";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'middle_name',
        'points',
        'phone',
        'sex',
        'dob',
        'city',
        'country',
        'user_uuid',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'dob' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function contactLists()
    {
        return $this->belongsToMany(ContactList::class, 'contact_contact_list', 'contact_uuid', 'contact_list_uuid')->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param ...$uuids
     * @return Builder
     */

    public function scopeUuidsNotIn(Builder $query, ...$uuids): Builder
    {
        return $query->whereNotIn('uuid', $uuids);
    }

    /**
     * @param Builder $query
     * @param ...$uuids
     * @return Builder
     */

    public function scopeUuidsIn(Builder $query, ...$uuids): Builder
    {
        return $query->whereIn('uuid', $uuids);
    }
}
