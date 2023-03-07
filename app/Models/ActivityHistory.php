<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ActivityHistory extends AbstractModel
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * @var string
     */
    protected $table = "activity_histories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'type_id',
        'content',
        'date'
    ];

    /**
     * @var string[]
     */
    public $translatable = ['content'];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'datetime',
        'content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
