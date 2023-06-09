<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AssetGroup extends Model
{

    use HasFactory, SoftDeletes, HasSlug;

    /**
     * @var string
     */
    protected $table = "asset_groups";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('code')
            ->slugsShouldBeNoLongerThan(50)
            ->usingSeparator('_');
    }

}
