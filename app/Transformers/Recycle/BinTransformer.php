<?php

namespace App\Transformers\Recycle;

use App\Models\Bin;
use League\Fractal\TransformerAbstract;

class BinTransformer extends TransformerAbstract
{
    public function transform(Bin $bin)
    {
        return [
            'id' => $bin->id,
            'site_id' => $bin->site_id,
            'name' => $bin->name,
            'no' => $bin->no,
            'address' => $bin->address,
            'site' => $bin->site,
            'type_paper'=> $bin->type_paper,
            'type_fabric'=> $bin->type_fabric,
        ];
    }


}