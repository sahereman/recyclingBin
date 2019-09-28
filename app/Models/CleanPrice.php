<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanPrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'price',
        'unit',
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

    protected $appends = [
    ];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    public function getSlugTextAttribute()
    {
        $slug = title_case($this->slug);
        $className = '\App\Models\BinType' . $slug;
        $type = new $className;
        return $type::NAME;
    }
}
