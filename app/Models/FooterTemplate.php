<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterTemplate extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;

    /**
     * @var string[]
     */
    protected $table = "footer_templates";

    /**
     * @var string[]
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'template',
        'template_json',
        'user_uuid',
        'publish_status',
        'is_default',
        'active_by_uuid',
        'type'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'template_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' =>  'integer',
        'active_by_uuid' =>  'integer',
        'is_default' => 'boolean',
    ];

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
    public function activeBy()
    {
        return $this->belongsTo(User::class, 'active_by_uuid', 'uuid');
    }
}
