<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Abstracts\AbstractService;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends AbstractModel
{
    use HasFactory, SoftDeletes;

    CONST CAMPAIGN_BIRTHDAY_TYPE = 'birthday';
    CONST CAMPAIGN_SIMPLE_TYPE = 'simple';
    CONST CAMPAIGN_SCENARIO_TYPE = 'scenario';

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
        'status',
        'type',
        'smtp_account_uuid',
        'send_project_uuid',
        'user_uuid',
        'reply_to_email',
        'reply_name',
        'was_finished',
        'was_stopped_by_owner',
        'send_type',
        'send_from_name',
        'send_from_email',
        'app_id',
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
        'was_finished' => 'boolean',
        'was_stopped_by_owner' => 'boolean',
        'mail_template_uuid' =>  'integer',
        'smtp_account_uuid' =>  'integer',
        'send_project_uuid' =>  'integer'

    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'number_credit_needed_to_start_campaign',
        'is_expired'
    ];

    /**
     * @return BelongsTo
     */
    public function smtpAccount()
    {
        return $this->belongsTo(SmtpAccount::class, 'smtp_account_uuid', 'uuid')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function mailTemplate()
    {
        return $this->belongsTo(MailTemplate::class, 'mail_template_uuid', 'uuid')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function sendProject()
    {
        return $this->belongsTo(SendProject::class, 'send_project_uuid', 'uuid')->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function mailSendingHistories()
    {
        return $this->hasMany(MailSendingHistory::class, 'campaign_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function sendEmailScheduleLogs()
    {
        return $this->hasMany(SendEmailScheduleLog::class, 'campaign_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid')->withTrashed();
    }

    /**
     * @return BelongsToMany
     */
    public function contactLists()
    {
        return $this->belongsToMany(ContactList::class, 'campaign_contact_list', 'campaign_uuid', 'contact_list_uuid')->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function creditTransactionHistories()
    {
        return $this->hasMany(CreditTransactionHistory::class, 'campaign_uuid', 'uuid');
    }

    /**
     * @return HasMany
     */
    public function campaignsScenario()
    {
        return $this->hasMany(CampaignScenario::class, 'campaign_uuid', 'uuid');
    }

    /**
     * @return mixed
     */
    public function getNumberCreditNeededToStartCampaignAttribute()
    {
        return app(CampaignService::class)->numberOfCreditsToStartTheCampaign($this->uuid, $this->from_date, $this->to_date, $this->send_type, $this->type);
    }

    /**
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        $timezone = (new CampaignService())->getConfigByKeyInCache('timezone')->value;
        return $this->to_date->toDateTimeString() < Carbon::now($timezone)->toDateTimeString();
    }

    public function scopeCampaignIsExpired(Builder $query, $check)
    {
        $timezone = (new CampaignService())->getConfigByKeyInCache('timezone')->value;
        if ($check){
            return $query->where('to_date', '<',  Carbon::now($timezone));
        }else{
            return $query->where('to_date', '>=',  Carbon::now($timezone));
        }
    }

    /**
     * @param Builder $query
     * @param $fromDate
     * @return Builder
     */
    public function scopeFromFromDate(Builder $query, $fromDate): Builder
    {
        return $query->whereDate('from_date', '>=', $fromDate);
    }

    /**
     * @param Builder $query
     * @param $fromDate
     * @return Builder
     */
    public function scopeToFromDate(Builder $query, $fromDate): Builder
    {
        return $query->whereDate('from_date', '<=', $fromDate);
    }

    /**
     * @param Builder $query
     * @param $toDate
     * @return Builder
     */
    public function scopeFromToDate(Builder $query, $toDate): Builder
    {
        return $query->whereDate('to_date', '>=', $toDate);
    }

    /**
     * @param Builder $query
     * @param $toDate
     * @return Builder
     */
    public function scopeToToDate(Builder $query, $toDate): Builder
    {
        return $query->whereDate('to_date', '<=', $toDate);
    }
}
