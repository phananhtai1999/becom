<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use App\Services\UserProfileService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Purpose extends AbstractModel
{
    use HasFactory,
        SoftDeletes, HasTranslations,
        ModelFilterLanguageTrait;

    const PUBLISHED_PUBLISH_STATUS = 1;
    const PENDING_PUBLISH_STATUS = 2;


    /**
     * @var string
     */
    protected $table = "purposes";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'publish_status',
        'title'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'title' => 'array'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'titles',
    ];

    public function mailTemplates()
    {
        return $this->hasMany(MailTemplate::class, 'purpose_uuid', 'uuid');
    }

    /**
     * @return array|mixed
     */
    public function getTitlesAttribute()
    {
        return app(UserProfileService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title;
    }
}
