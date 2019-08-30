<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSite extends Model
{
    protected $fillable = [
        'name',
        'county',
        'province',
        'province_simple',
        'city',
        'city_simple',
    ];

    protected $casts = [
    ];

    protected $dates = [
    ];
}
