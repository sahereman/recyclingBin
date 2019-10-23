<?php

namespace App\Transformers\Client;

use App\Models\Banner;
use League\Fractal\TransformerAbstract;

class BannerTransformer extends TransformerAbstract
{
    public function transform(Banner $banner)
    {
        return [
            'id' => $banner->id,
            'slug' => $banner->slug,
            'image_url' => $banner->image_url,
            'link' => $banner->link,
            'sort' => $banner->sort,
        ];
    }


}