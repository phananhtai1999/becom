<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;
    const DRAFT_PUBLISH_STATUS = 4;

    /**
     * @var string
     */
    protected $table = "forms";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'html_template',
        'css_template',
        'js_template',
        'template_json',
        'contact_list_uuid',
        'user_uuid',
        'publish_status',
        'display_type',
        'reject_reason',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'contact_list_uuid' => 'integer',
        'template_json' => 'array',
        'reject_reason' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return BelongsTo
     */
    public function contactList()
    {
        return $this->belongsTo(ContactList::class, 'contact_list_uuid', 'uuid');
    }
}
