<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const NOTE_TYPE = 'note';
    const NOTE_CREATED_ACTION = 'created';
    const NOTE_UPDATED_ACTION = 'updated';
    const NOTE_DELETED_ACTION = 'deleted';

    /**
     * @var string
     */
    protected $table = "notes";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'note',
        'user_uuid',
        'contact_uuid',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_uuid', 'uuid');
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
        return $this->hasMany(ActivityHistory::class, 'type_id', 'uuid')->where('type', 'note');
    }
}
