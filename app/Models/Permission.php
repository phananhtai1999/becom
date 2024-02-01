<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactNameLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterNameLanguageTrait;
use App\Services\UserProfileService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Permission extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations, ModelFilterExactNameLanguageTrait, ModelFilterNameLanguageTrait;

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
        'names',
    ];

    public function platformPackages() {
        return $this->belongsToMany(App::class, 'platform_package_permission', 'permission_uuid', 'platform_package_uuid');
    }

    /**
     * @return array|mixed
     */
    public function getNamesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('name') : $this->name;
    }

    public function addOns() {
        return $this->belongsToMany(AddOn::class, 'add_on_permissions', 'permission_uuid', 'add_on_uuid');
    }
}
