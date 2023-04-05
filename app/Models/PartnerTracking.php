<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerTracking extends AbstractModel
{
    use HasFactory;

    protected $table = "partner_trackings";

    protected $primaryKey = "uuid";

    protected $fillable = [
        'ip',
        'partner_uuid',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'partner_uuid' => 'integer'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_uuid', 'uuid');
    }
}
