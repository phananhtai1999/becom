<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;

class Language extends AbstractModel
{
    use HasFactory;

    CONST LANGUAGES_SUPPORT = [
      'vi','en','fr','ch'
    ];

    protected $table = 'languages';

    protected $primaryKey = "code";

    public $incrementing = false;

    protected $keyType = "string";

    public $languagesSupport = "";

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


    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->languagesSupport = array_map('basename', File::directories(resource_path('lang')));
    }


}
