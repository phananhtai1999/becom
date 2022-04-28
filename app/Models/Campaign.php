<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "campaigns";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'tracking_key',
        'mail_template_uuid',
        'from_date',
        'to_date',
        'number_email_per_date',
        'number_email_per_user',
        'status',
        'smtp_account_uuid',
        'website_uuid',
        'is_running'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'is_running' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function smtpAccount()
    {
        return $this->belongsTo(SmtpAccount::class, 'smtp_account_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function mailTemplate()
    {
        return $this->belongsTo(MailTemplate::class, 'mail_template_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class, 'website_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function mailSendingHistories()
    {
        return $this->hasMany(MailSendingHistory::class, 'campaign_uuid', 'uuid');
    }
}
