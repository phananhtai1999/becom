<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionTemplate extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;

    /**
     * @var string
     */
    protected $table = "section_templates";

    /**
     * @var string
     */
    protected $primaryKey = "uuid";

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
        'section_category_uuid',
        'reject_reason'
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
        'section_category_uuid' => 'integer',
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
    public function sectionCategory()
    {
        return $this->belongsTo(SectionCategory::class, 'section_category_uuid', 'uuid',);
    }
}
