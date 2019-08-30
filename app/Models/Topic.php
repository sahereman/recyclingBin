<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [

    ];

    protected $casts = [
        'is_index' => 'boolean',
    ];

    protected $dates = [
    ];
}
