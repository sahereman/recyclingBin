<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinRecycler extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'bin_id',
        'recycler_id',
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
        //
    ];

    /* Eloquent Relationships */
    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function recycler()
    {
        return $this->belongsTo(Recycler::class);
    }
}
