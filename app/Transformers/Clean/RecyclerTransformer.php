<?php

namespace App\Transformers\Clean;

use App\Models\Recycler;
use League\Fractal\TransformerAbstract;

class RecyclerTransformer extends TransformerAbstract
{
    public function transform(Recycler $recycler)
    {
        return [
            'id' => $recycler->id,
            'name' => $recycler->name,
            'phone' => $recycler->phone,
            'avatar_url' => $recycler->avatar_url,
            'money' => $recycler->money,
            'frozen_money' => $recycler->frozen_money,
            'notification_count' => $recycler->notification_count,

            'wx_openid' => $recycler->wx_openid,
            'created_at' => $recycler->created_at->toDateTimeString(),
            'updated_at' => $recycler->updated_at->toDateTimeString(),
        ];
    }


}