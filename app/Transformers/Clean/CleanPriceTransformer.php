<?php

namespace App\Transformers\Clean;

use App\Models\CleanOrder;
use App\Models\CleanPrice;
use League\Fractal\TransformerAbstract;

class CleanPriceTransformer extends TransformerAbstract
{
    public function transform(CleanPrice $price)
    {
        return [
            'id' => $price->id,
            'slug' => $price->slug,
            'price' => $price->price,
            'unit' => $price->unit,
        ];
    }


}