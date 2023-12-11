<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Order;
use App\Models\QueryBuilders\MyOrderQueryBuilder;

class MyOrderService extends AbstractService
{
    protected $modelClass = Order::class;

    protected $modelQueryBuilderClass = MyOrderQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyOrder($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyOrderById($id)
    {
        $order = $this->showMyOrder($id);

        return $this->destroy($order->getKey());
    }
}
