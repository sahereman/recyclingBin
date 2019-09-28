<?php

namespace App\Admin\Models;

use App\Models\ClientPrice as ClientPriceModel;

class ClientPrice extends ClientPriceModel
{
    protected $appends = [
        'slug_text',
    ];
}
