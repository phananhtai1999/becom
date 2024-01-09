<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remind extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const REMIND_TYPE = 'remind';
    const REMIND_CREATED_ACTION = 'created';
    const REMIND_UPDATED_ACTION = 'updated';
    const REMIND_DELETED_ACTION = 'deleted';

    /**
     * @var string
     */
    protected $table = "reminds";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'note',
        'date',
        'user_uuid',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_remind', 'remind_uuid', 'contact_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activityHistories()
    {
        return $this->hasMany(ActivityHistory::class, 'type_id', 'uuid')->where('type', 'remind');
    }
}
