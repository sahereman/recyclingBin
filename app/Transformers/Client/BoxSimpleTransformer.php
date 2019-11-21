<?php

namespace App\Transformers\Client;

use App\Models\Box;
use League\Fractal\TransformerAbstract;

class BoxSimpleTransformer extends TransformerAbstract
{
    public function transform(Box $box)
    {
        return [
            'id' => $box->id,
            'site_id' => $box->site_id,
            'status' => $box->status,
            'status_text' => $box->status_text,
            'name' => $box->name,
            'no' => $box->no,
            'address' => $box->address,
            'distance' => $box->distance ?? 0,
            'lat' => $box->lat,
            'lng' => $box->lng,
        ];
    }


}