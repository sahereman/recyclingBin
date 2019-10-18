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
        'fabric_permission',
        'paper_permission',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'fabric_permission' => 'boolean',
        'paper_permission' => 'boolean',

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
