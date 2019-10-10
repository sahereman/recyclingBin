<?php

namespace App\Transformers\Clean;

use App\Models\CleanOrder;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(CleanOrder $order)
    {
        return [
            'id' => $order->id,
            'recycler_id' => $order->recycler_id,
            'sn' => $order->sn,
            'status' => $order->status,
            'status_text' => $order->status_text,
            'bin_name' => $order->bin_snapshot['name'],
            'total' => $order->total,
            'created_at' => $order->created_at->format('Y年m月d日 H:i'),
            'items' => $order->items,
        ];
    }


}