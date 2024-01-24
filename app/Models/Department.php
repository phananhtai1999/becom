<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactNameLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterNameLanguageTrait;
use App\Http\Requests\AddDepartmentForBusinessRequest;
use App\Services\UserProfileService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Department extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations, ModelFilterNameLanguageTrait, ModelFilterExactNameLanguageTrait;
    const DEFAULT_NAME = ['hr', 'marketing', 'it'];
    /**
     * @var string
     */
    protected $table = "departments";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'business_uuid',
        'location_uuid',
        'user_uuid',
        'app_id',
        'manager_uuid',
        'is_default',
        'status',
    ];

    /**
     * @var string[]
     */
    public $translatable = ['name'];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'names',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'company_contact', 'department_uuid', 'contact_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    public function manager()
    {
        return $this->belongsTo(UserProfile::class, 'manager_uuid', 'user_uuid');
    }

    public function business()
    {
        return $this->belongsTo(BusinessManagement::class, 'business_uuid', 'uuid');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_uuid', 'uuid');
    }

    /**
     * @return array|mixed
     */
    public function getNamesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermissionWithAdminAndRootRole() ? $this->getTranslations('name') : $this->name;
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'department_uuid', 'uuid');
    }

    public function sendProjects()
    {
        return $this->belongsToMany(SendProject::class, 'department_send_project', 'department_uuid', 'send_project_uuid')->withTimestamps();
    }

}
