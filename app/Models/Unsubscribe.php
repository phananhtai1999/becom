<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unsubscribe extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "unsubscribes";

    /**
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'contact_uuid',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'contact_uuid' => 'integer'
    ];

    /**
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_uuid', 'uuid');
    }


}
