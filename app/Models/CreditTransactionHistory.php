<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * @param Builder $query
     * @param $data
     * @return void
     */
    public function scopeAddByEmail(Builder $query, $data) {
//        $query->whereRaw("IF (add_by_uuid is NOT NULL,
//                        add_by_uuid IN (SELECT b.uuid FROM `users` b
//                                        WHERE add_by_uuid = b.uuid and b.email = '{$data}'),
//                        user_uuid IN (SELECT c.uuid FROM `users` c
//                                        WHERE user_uuid = c.uuid and c.email = '{$data}'))");

        $query->where(function ($query) use ($data){
            $query->whereNotNull('add_by_uuid')
                ->whereIn('add_by_uuid', function ($query) use ($data){
                    $query->select('users.uuid')
                        ->from('users')
                        ->whereColumn('add_by_uuid', 'users.uuid')
                        ->where('users.email', $data);
                });
        })->orWhere(function ($query) use ($data){
            $query->whereNull('add_by_uuid')
                ->whereIn('user_uuid', function ($query) use ($data){
                    $query->select('users.uuid')
                        ->from('users')
                        ->whereColumn('user_uuid', 'users.uuid')
                        ->where('users.email', $data);
                });
        });
    }
}
