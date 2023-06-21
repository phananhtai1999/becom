<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsitePage extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;

    /**
     * @var string
     */
    protected $table = "website_pages";

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
        'website_page_category_uuid',
        'display_type',
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
        'website_page_category_uuid' => 'integer',
        'is_default' => 'boolean',
        'reject_reason' => 'array',
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
    public function websitePageCategory()
    {
        return $this->belongsTo(WebsitePageCategory::class, 'website_page_category_uuid', 'uuid',);
    }

    public function websites()
    {
        return $this->belongsToMany(Website::class, 'website_website_page', 'website_page_uuid', 'website_uuid')->withPivot(['is_homepage', 'ordering'])->withTimestamps();
    }
}
