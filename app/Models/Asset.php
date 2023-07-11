<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const PENDING_STATUS = 'pending';
    const PUBLISH_STATUS = 'publish';

    const REJECT_STATUS = 'reject';
    const DRAFT_STATUS = 'draft';
    /**
     * @var string
     */
    protected $table = "assets";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'title',
        'asset_size_uuid',
        'url',
        'status',
        'user_uuid',
        'reject_reason'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'reject_reason' => 'array',
    ];

    public function assetSize()
    {
        return $this->hasOne(AssetSize::class, 'uuid', 'asset_size_uuid');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'user_uuid');
    }
}
