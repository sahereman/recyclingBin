<?php

namespace App\Admin\Models;

use App\Models\Bin as BinModel;

class Bin extends BinModel
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_run' => 'boolean',
        // 'types_snapshot' => 'json',
    ];
}
