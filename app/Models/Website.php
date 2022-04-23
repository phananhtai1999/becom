<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends AbstractModel
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "websites";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'domain',
        'user_uuid',
        'name',
        'description',
        'logo',
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
     * @return mixed
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'website_uuid', 'uuid');
    }

    /**
     * @return mixed
     */
    public function emails()
    {
        return $this->hasMany(Email::class, 'website_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smtpAccounts()
    {
        return $this->hasMany(SmtpAccount::class, 'website_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websiteVerifications()
    {
        return $this->hasMany(WebsiteVerification::class, 'website_uuid', 'uuid');
    }
}
