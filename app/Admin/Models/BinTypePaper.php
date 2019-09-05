<?php

namespace App\Admin\Models;

use App\Models\BinTypePaper as BinTypePaperModel;

class BinTypePaper extends BinTypePaperModel
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'client_price_value',
        'recycle_price_value'
    ];
}
