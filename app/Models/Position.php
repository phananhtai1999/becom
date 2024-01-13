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

class Position extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations, ModelFilterNameLanguageTrait, ModelFilterExactNameLanguageTrait;

    /**
     * @var string
     */
    protected $table = "positions";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_uuid',
        'app_id',
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
        return $this->belongsToMany(Contact::class, 'company_contact', 'position_uuid', 'contact_uuid')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return array|mixed
     */
    public function getNamesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermissionWithAdminAndRootRole() ? $this->getTranslations('name') : $this->name;
    }
}
