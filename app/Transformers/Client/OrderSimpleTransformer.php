<?php

namespace App\Transformers\Client;

use App\Models\Bin;
use App\Models\ClientOrder;
use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class OrderSimpleTransformer extends TransformerAbstract
{
    public function transform(ClientOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'status_text' => $order->status_text,
            'bin_name' =>  $order->bin_snapshot['name'],
            'total' => $order->total,
            'created_at' => $order->created_at->toDateTimeString(),
            'items' => $order->items,
        ];
    }


}