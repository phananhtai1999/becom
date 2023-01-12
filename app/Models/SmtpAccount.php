<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmtpAccount extends AbstractModel
{
    use HasFactory, SoftDeletes;

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
        'website_uuid',
        'user_uuid'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'website_uuid' =>  'integer',
        'user_uuid' =>  'integer',
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
    public function website()
    {
        return $this->belongsTo(Website::class, 'website_uuid', 'uuid');
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
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
