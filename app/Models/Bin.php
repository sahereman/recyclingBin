<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $fillable = [

    ];

    protected $casts = [
        'is_run' => 'boolean',
        'types_snapshot' => 'json',
    ];

    protected $dates = [
    ];
}
