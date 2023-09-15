<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends AbstractModel
{
    use HasFactory;

    protected $table = 'languages';

    protected $primaryKey = "code";

    public $incrementing = false;

    protected $keyType = "string";

    protected $fillable = [
        'code',
        'name',
        'fe',
        'status',
        'flag_image',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'boolean',
    ];
}
