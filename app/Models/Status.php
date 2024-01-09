<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterExactNameLanguageTrait;
use App\Http\Controllers\Traits\ModelFilterNameLanguageTrait;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Status extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations, ModelFilterNameLanguageTrait, ModelFilterExactNameLanguageTrait;

    const STATUS_CLIENT = 'Client';
    const STATUS_LEAD = 'Lead';
    const STATUS_CUSTOMER = 'Customer';
    const STATUS_LOYAL_CUSTOMER = 'Loyal Customer';

    /**
     * @var string
     */
    protected $table = "status";

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
        'points',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'status_uuid', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_uuid', 'user_uuid');
    }

    /**
     * @return mixed
     */
    public function getNamesAttribute()
    {
        return app(UserService::class)->checkLanguagesPermissionWithAdminAndRootRole() ? $this->getTranslations('name') : $this->name;
    }
}
