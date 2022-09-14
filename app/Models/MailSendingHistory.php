<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailSendingHistory extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const SENT = 'sent';
    const FAIL = 'fail';
    const RECEIVED = 'received';
    const OPENED = 'opened';

    /**
     * @var string[]
     */
    protected $table = "mail_sending_history";

    /**
     * @var string[]
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'campaign_uuid',
        'email',
        'time',
        'status'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'time' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'uuid')->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function mailOpenTrackings()
    {
        return $this->hasMany(MailOpenTracking::class, 'mail_sending_history_uuid', 'uuid');
    }
}
