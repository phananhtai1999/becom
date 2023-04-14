<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;

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
        'design',
        'publish_status',
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
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'rendered_body'
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
}
