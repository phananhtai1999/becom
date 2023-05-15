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
    const PENDING_PUBLISH_STATUS = 2;

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
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' =>  'integer',
        'footer_section_uuid' =>  'integer',
        'header_section_uuid' =>  'integer',
        'domain_uuid' =>  'integer',
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
        return $this->belongsToMany(WebsitePage::class, 'website_website_page', 'website_uuid', 'website_page_uuid')->withPivot(['is_homepage', 'ordering'])->withTimestamps();
    }
}
