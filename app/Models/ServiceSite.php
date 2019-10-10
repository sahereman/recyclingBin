<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'county',
        'province',
        'province_simple',
        'city',
        'city_simple',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        //
    ];

    /* Eloquent Relationships */
    public function bins()
    {
        return $this->hasMany(Bin::class, 'site_id');
    }
}
