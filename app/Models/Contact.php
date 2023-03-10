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
        'avatar',
        'status_uuid'
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
     * @var string[]
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' '. $this->middle_name . ' '. $this->last_name;
    }

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

    /**
     * @param Builder $query
     * @param $dob
     * @return Builder
     */
    public function scopeFromDob(Builder $query, $dob): Builder
    {
        return $query->whereDate('dob', '>=', $dob);
    }

    /**
     * @param Builder $query
     * @param $dob
     * @return Builder
     */
    public function scopeToDob(Builder $query, $dob): Builder
    {
        return $query->whereDate('dob', '<=', $dob);
    }

    /**
     * @return BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_uuid', 'uuid');
    }

    /**
     * @return BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_contact', 'contact_uuid', 'company_uuid')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'company_contact', 'contact_uuid', 'position_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'contact_uuid', 'uuid');
    }

    /**
     * @return BelongsToMany
     */
    public function reminds()
    {
        return $this->belongsToMany(Remind::class, 'contact_remind', 'contact_uuid', 'remind_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activityHistories()
    {
        return $this->hasMany(ActivityHistory::class, 'contact_uuid', 'uuid');
    }
}
