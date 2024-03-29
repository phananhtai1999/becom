<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmtpAccount extends AbstractModel
{
    use HasFactory, SoftDeletes;
    CONST SMTP_ACCOUNT_SMTP_MAIL_MAILER = 'smtp';
    CONST SMTP_ACCOUNT_TELEGRAM_MAIL_MAILER = 'telegram';
    CONST SMTP_ACCOUNT_VIBER_MAIL_MAILER = 'viber';

    /**
     * @var string
     */
    protected $table = "smtp_accounts";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'smtp_mail_encryption_uuid',
        'mail_from_address',
        'mail_from_name',
        'secret_key',
        'send_project_uuid',
        'user_uuid',
        'status',
        'publish',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'send_project_uuid' =>  'integer',
        'publish' => 'boolean'
    ];

    /**
     * @return mixed
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'smtp_account_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function sendProject()
    {
        return $this->belongsTo(SendProject::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function smtpAccountEncryption()
    {
        return $this->belongsTo(SmtpAccountEncryption::class, 'smtp_mail_encryption_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }
}
