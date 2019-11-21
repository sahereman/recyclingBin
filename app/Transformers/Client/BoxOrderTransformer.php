<?php

namespace App\Transformers\Client;

use App\Models\BoxOrder;
use League\Fractal\TransformerAbstract;

class BoxOrderTransformer extends TransformerAbstract
{
    public function transform(BoxOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'status_text' => $order->status_text,
            'image_proof_url' => $order->image_proof_url,
            'total' => $order->total,
            'created_at' => $order->created_at->toDateTimeString(),
        ];
    }


}