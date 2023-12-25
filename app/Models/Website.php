<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends AbstractModel
{
    use HasFactory;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const BLOCKED_PUBLISH_STATUS = 2;
    const PENDING_PUBLISH_STATUS = 3;
    const REJECT_PUBLISH_STATUS = 4;
    const DRAFT_PUBLISH_STATUS = 5;

    /**
     * @var string[]
     */
    protected $table = "websites";

    /**
     * @var string[]
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'header_section_uuid',
        'footer_section_uuid',
        'description',
        'user_uuid',
        'publish_status',
        'logo',
        'domain_uuid',
        'tracking_ids',
        'is_active_news_page',
        'app_id',
        'is_default',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' => 'integer',
        'footer_section_uuid' => 'integer',
        'header_section_uuid' => 'integer',
        'domain_uuid' => 'integer',
        'tracking_ids' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function headerSection()
    {
        return $this->belongsTo(SectionTemplate::class, 'header_section_uuid', 'uuid');
    }

    public function footerSection()
    {
        return $this->belongsTo(SectionTemplate::class, 'footer_section_uuid', 'uuid');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'uuid');
    }

    public function websitePages()
    {
        return $this->belongsToMany(WebsitePage::class, 'website_website_page', 'website_uuid', 'website_page_uuid')->withPivot(['is_homepage', 'ordering'])->select($this->getFillablesWebsitePage())->withTimestamps();
    }

    public function websitePagesPublic()
    {
        return $this->belongsToMany(WebsitePage::class, 'website_website_page', 'website_uuid', 'website_page_uuid')->withPivot(['is_homepage', 'ordering'])->where('publish_status', WebsitePage::PUBLISHED_PUBLISH_STATUS)->select($this->getFillablesWebsitePage())->withTimestamps();
    }

    public function getFillablesWebsitePage()
    {
        $modelKeyName = (new WebsitePage())->getKeyName();

        return array_diff(array_merge((new WebsitePage())->getFillable(), ["website_pages.$modelKeyName", 'website_pages.deleted_at', 'website_pages.created_at', 'website_pages.updated_at']), request()->get('exclude_website_page', []));
    }
}
