<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetSize extends Model
{

    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "asset_sizes";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'width',
        'height',
        'asset_group_uuid',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function assetGroup(){
        return $this->hasOne(AssetGroup::class, 'uuid', 'asset_group_uuid');
    }
}
