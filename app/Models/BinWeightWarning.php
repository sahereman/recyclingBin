<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinWeightWarning extends Model
{

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'bin_id',
        'type_slug',
        'type_name',
        'normal_weight',
        'measure_weight',
        'exception_weight',
        'unit',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        //
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
    ];


    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
