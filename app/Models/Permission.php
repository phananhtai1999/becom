<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
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
        'code'
    ];

    protected $casts = [
        'name' => 'array'
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
}
