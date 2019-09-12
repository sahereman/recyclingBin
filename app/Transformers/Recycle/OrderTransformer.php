<?php

namespace App\Transformers\Recycle;

use App\Models\CleanOrder;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(CleanOrder $order)
    {
        return [
            'id' => $order->id,
            'recycler_id' => $order->recycler_id,
            'status' => $order->status,
            'status_text' => $order->status_text,
            'bin_name' => $order->bin_snapshot['name'],
            'total' => $order->total,
            'items' => $order->items,
        ];
    }


}