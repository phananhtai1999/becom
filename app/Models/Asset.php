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
        'asset_group_code',
        'asset_size_uuid',
        'url',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function assetSize()
    {
        return $this->hasOne(AssetSize::class, 'uuid', 'asset_size_uuid');
    }
}
