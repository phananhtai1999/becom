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
    public function scopeName(Builder $query, ...$names)
    {
        return $query->where(function ($q) use($names){
            $lang = app()->getLocale();
            $langDefault = config('app.fallback_locale');
            foreach ($names as $name){
                $q->orWhereRaw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang')),JSON_UNQUOTE(JSON_EXTRACT(name, '$.$langDefault'))) like '%$name%'");
            }
        });
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

    public function addOns() {
        return $this->belongsToMany(AddOn::class, 'add_on_permissions', 'permission_uuid', 'add_on_uuid');
    }
}
