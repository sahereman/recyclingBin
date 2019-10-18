<?php

namespace App\Transformers\Clean;

use App\Models\Bin;
use League\Fractal\TransformerAbstract;

class BinTransformer extends TransformerAbstract
{
    public function transform(Bin $bin)
    {
        $type_paper = $bin->type_paper->toArray();
        $type_paper['subtotal'] = bcmul($type_paper['number'], $type_paper['clean_price']['price'], 2);

        $type_fabric = $bin->type_fabric->toArray();
        $type_fabric['subtotal'] = bcmul($type_fabric['number'], $type_fabric['clean_price']['price'], 2);

        return [
            'id' => $bin->id,
            'site_id' => $bin->site_id,
            'name' => $bin->name,
            'no' => $bin->no,
            'address' => $bin->address,
            'total' => bcadd($type_paper['subtotal'], $type_fabric['subtotal'], 2),
            'lat' => $bin->lat,
            'lng' => $bin->lng,
            'site' => $bin->site,
            'type_paper' => $type_paper,
            'type_fabric' => $type_fabric,
        ];
    }


}