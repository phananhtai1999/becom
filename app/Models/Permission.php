<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Permission extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * @var string
     */
    protected $table = "permissions";

    public $translatable = ['name'];

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'api_methods'
    ];

    protected $casts = [
        'name' => 'array',
        'api_methods' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name_translate',
    ];

    /**
     * @param Builder $query
     * @param $name
     * @return Builder
     */
    public function scopeName(Builder $query, $name)
    {
        $lang = app()->getLocale();
        return $query->where("name->$lang", 'like', "%$name%");
    }

    public function platformPackages() {
        return $this->belongsToMany(PlatformPackage::class, 'platform_package_permission', 'permission_uuid', 'platform_package_uuid');
    }

    /**
     * @return array|mixed
     */
    public function getNameTranslateAttribute()
    {
        return app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('name') : $this->name;
    }
}
