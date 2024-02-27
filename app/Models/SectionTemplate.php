<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionTemplate extends AbstractModel
{
    use HasFactory, SoftDeletes;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;
    const REJECT_PUBLISH_STATUS = 3;
    const DRAFT_PUBLISH_STATUS = 4;

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
        'html_template',
        'css_template',
        'js_template',
        'template_json',
        'user_uuid',
        'publish_status',
        'is_default',
        'section_category_uuid',
        'reject_reason',
        'type',
        'app_id',
        'display_mode'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'template_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'section_category_uuid' => 'integer',
        'is_default' => 'boolean',
        'reject_reason' => 'array',
    ];

    public function headerWebsite()
    {
        return $this->hasOne(Website::class, 'header_section_uuid', 'uuid');
    }

    public function footerWebsite()
    {
        return $this->hasOne(Website::class, 'footer_section_uuid', 'uuid');
    }

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
    public function sectionCategory()
    {
        return $this->belongsTo(SectionCategory::class, 'section_category_uuid', 'uuid',);
    }

    /**
     * @param Builder $query
     * @param ...$titles
     * @return Builder
     */
    public function scopeSectionCategoryTitle(Builder $query, ...$titles)
    {
        return $query->where('section_category_uuid', function ($q) use ($titles) {
            $q->select('a.uuid')
                ->from('section_categories as a')
                ->whereColumn('a.uuid', 'section_templates.section_category_uuid')
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
