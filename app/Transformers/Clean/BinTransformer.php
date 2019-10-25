<?php

namespace App\Transformers\Clean;

use App\Models\Bin;
use League\Fractal\TransformerAbstract;

class BinTransformer extends TransformerAbstract
{
    public function transform(Bin $bin)
    {
        $total = '0.00';
        $type_paper = $bin->type_paper->toArray();
        $type_paper['permission'] = $bin->pivot->paper_permission;
        $type_fabric = $bin->type_fabric->toArray();
        $type_fabric['permission'] = $bin->pivot->fabric_permission;

        if ($bin->pivot->paper_permission)
        {
            $type_paper['subtotal'] = bcmul($type_paper['number'], $type_paper['clean_price']['price'], 2);
            $total = bcadd($total, $type_paper['subtotal'], 2);
        } else
        {
            $type_paper['number'] = '****';
            $type_paper['subtotal'] = '****';
        }

        if ($bin->pivot->fabric_permission)
        {
            $type_fabric['subtotal'] = bcmul($type_fabric['number'], $type_fabric['clean_price']['price'], 2);
            $total = bcadd($total, $type_fabric['subtotal'], 2);
        } else
        {
            $type_fabric['number'] = '****';
            $type_fabric['subtotal'] = '****';
        }

        return [
            'id' => $bin->id,
            'site_id' => $bin->site_id,
            'name' => $bin->name,
            'no' => $bin->no,
            'address' => $bin->address,
            'total' => $total,
            'lat' => $bin->lat,
            'lng' => $bin->lng,
            'site' => $bin->site,
            'type_paper' => $type_paper,
            'type_fabric' => $type_fabric,
        ];
    }


}