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

            'created_at' => $recycler->created_at->toDateTimeString(),
            'updated_at' => $recycler->updated_at->toDateTimeString(),
        ];
    }


}