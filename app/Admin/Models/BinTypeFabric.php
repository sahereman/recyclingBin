<?php

namespace App\Admin\Models;

use App\Models\BinTypeFabric as BinTypeFabricModel;

class BinTypeFabric extends BinTypeFabricModel
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'client_price_value',
        'clean_price_value'
    ];
}
