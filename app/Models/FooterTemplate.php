<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
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
        'template_type',
        'template',
        'template_json',
        'user_uuid',
        'publish_status',
        'is_default',
        'active_by_uuid',
        'type',
        'app_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'template_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'active_by_uuid' =>  'integer',
        'is_default' => 'boolean',
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
    public function activeBy()
    {
        return $this->belongsTo(UserProfile::class, 'active_by_uuid', 'user_uuid');

    }

    public function scopeGetFooterByRole(Builder $query, $role)
    {
        $usersByRoles = ((new UserService())->getUsersByRole($role))->pluck('uuid');
        return $query->whereIn('user_uuid', $usersByRoles);
    }
}
