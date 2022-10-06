<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransactionHistory extends AbstractModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "transactions";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function add_by() {
        return $this->belongsTo(User::class, 'add_by_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function campaign() {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'uuid');
    }
}
