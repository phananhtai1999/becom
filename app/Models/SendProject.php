<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactFieldTrait;
use App\Http\Controllers\Traits\ModelFilterFieldTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendProject extends AbstractModel
{
    use HasFactory, SoftDeletes, ModelFilterFieldTrait, ModelFilterExactFieldTrait;

    /**
     * @var string
     */
    protected $table = "send_projects";

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
        'domain_uuid',
        'parent_uuid',
        'business_uuid'
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
     * @var string[]
     */
    protected $appends = ['was_verified'];

    /**
     * @return bool
     */
    public function getWasVerifiedAttribute()
    {
        return !empty($this->websiteVerification->verified_at);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @return BelongsToMany
     */
    public function emails()
    {
        return $this->belongsToMany(Email::class, 'website_email', 'send_project_uuid', 'email_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smtpAccounts()
    {
        return $this->hasMany(SmtpAccount::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function websiteVerification()
    {
        return $this->hasOne(WebsiteVerification::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mailTemplates()
    {
        return $this->hasMany(MailTemplate::class, 'send_project_uuid', 'uuid');
    }

    /**
     * @param Builder $query
     * @param $check
     * @return Builder
     */
    public function scopeDomainIsNull(Builder $query, $check)
    {
        if ($check) {
            return $query->whereNull('domain');
        }
        return $query->whereNotNull('domain');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domains()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(BusinessManagement::class, 'business_uuid', 'uuid');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_send_project', 'send_project_uuid', 'team_uuid')->withTimestamps();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_send_project', 'send_project_uuid', 'department_uuid')->withTimestamps();
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_send_project', 'send_project_uuid', 'location_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentSendProject()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return HasMany
     */
    public function childrenSendProject()
    {
        return $this->hasMany(__CLASS__, 'parent_uuid');
    }
}
