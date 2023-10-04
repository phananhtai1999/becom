<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const CONFIG_PUBLIC_STATUS = 'public';
    const CONFIG_SYSTEM_STATUS = 'system';
    const CONFIG_PRIVATE_STATUS = 'private';
    const CONFIG_SMS_PRICE = 'sms_price';
    const CONFIG_EMAIL_PRICE = 'email_price';
    const CONFIG_TELEGRAM_PRICE = 'telegram_price';
    const CONFIG_VIBER_PRICE = 'viber_price';
    const CONFIG_S3_SYSTEM = 's3_system';
    const CONFIG_S3_USER = 's3_user';
    const CONFIG_S3_WEBSITE = 's3_website';
    const CONFIG_MAILBOX_MX = 'mailbox_mx_domain';
    const CONFIG_MAILBOX_DMARC = 'mailbox_dmarc_domain';
    const CONFIG_MAILBOX_DKIM = 'mailbox_dkim_domain';
    const CONFIG_META_TAG_TYPE = 'meta_tag';
    const CONFIG_TRACKING_TYPE = 'tracking';

    /**
     * @var string
     */
    protected $table = "configs";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'key',
        'value',
        'default_value',
        'group_id',
        'type',
        'status'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'value' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'uuid');
    }

    /**
     * @return boolean
     */
    public function getValueFormattedAttribute()
    {
        if($this->type == 'boolean'){
            if($this->value === 0 || $this->value === '0' || $this->value === 'false' || $this->value === false){
                return false;
            }
            return true;
        }
        return $this->value;
    }
}
