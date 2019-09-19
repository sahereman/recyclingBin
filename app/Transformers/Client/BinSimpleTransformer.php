<?php

namespace App\Transformers\Client;

use App\Models\Bin;
use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class BinSimpleTransformer extends TransformerAbstract
{
    public function transform(Bin $bin)
    {
        return [
            'id' => $bin->id,
            'site_id' => $bin->site_id,
            'name' => $bin->name,
            'no' => $bin->no,
            'address' => $bin->address,
            'distance' => $bin->distance ?? 0,
            'lat' => $bin->lat,
            'lng' => $bin->lng,
            'types_snapshot' => $bin->types_snapshot,
        ];
    }


}