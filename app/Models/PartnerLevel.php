<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use App\Http\Controllers\Traits\ModelFilterLanguageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PartnerLevel extends AbstractModel
{
    use HasFactory, HasTranslations, ModelFilterLanguageTrait;

    /**
     * @var string
     */
    protected $table = "partner_levels";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'number_of_references',
        'commission'
    ];

    /**
     * @var string[]
     */
    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $casts = [
        'title' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'number_of_references' => 'integer',
        'commission' => 'integer'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'title_translate',
    ];

    /**
     * @return mixed
     */
    public function getTitleTranslateAttribute()
    {
        return $this->title;
    }
}
