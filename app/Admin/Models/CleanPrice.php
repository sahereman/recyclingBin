<?php

namespace App\Admin\Models;

use App\Models\CleanPrice as CleanPriceModel;

class CleanPrice extends CleanPriceModel
{
    protected $appends = [
        'slug_text',
    ];
}
