<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;

    /**
     * @var string[]
     */
    protected $table = "mail_templates";

    /**
     * @var string[]
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'subject',
        'body',
        'send_project_uuid',
        'user_uuid',
        'business_category_uuid',
        'purpose_uuid',
        'design',
        'publish_status',
        'reject_reason',
        'type',
        'image'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'design' => 'array',
        'image' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'send_project_uuid' => 'integer',
        'user_uuid' => 'integer',
        'business_category_uuid' => 'integer',
        'purpose_uuid' => 'integer',
        'reject_reason' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
//        'rendered_body'
    ];

    /**
     * @param $body
     * @return void
     */
    public function setRenderedBodyAttribute($body)
    {
        $this->rendered_body = $body;
    }

    /**
     * @return null|mixed
     */
    public function getRenderedBodyAttribute()
    {
        return $this->rendered_body ?? null;
    }

    /**
     * @return HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'mail_template_uuid', 'uuid');
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_uuid', 'uuid');
    }

    public function purpose()
    {
        return $this->belongsTo(Purpose::class, 'purpose_uuid', 'uuid');
    }

    public function scopeBusinessCategoryTitle(Builder $query, ...$titles)
    {
        return $query->where('business_category_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('business_categories as a')
                ->whereColumn('a.uuid', 'mail_templates.business_category_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }

    public function scopePurposeTitle(Builder $query, ...$titles)
    {
        return $query->where('purpose_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('purposes as a')
                ->whereColumn('a.uuid', 'mail_templates.purpose_uuid')
                ->where(function ($i) use ($titles) {
                    $lang = app()->getLocale();
                    $langDefault = config('app.fallback_locale');
                    foreach ($titles as $title) {
                        $i->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.$langDefault'))) = '$title'");
                    }
                });
        });
    }
}
