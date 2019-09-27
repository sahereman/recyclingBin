<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'site_id',
        'is_run',
        'name',
        'no',
        'lat',
        'lng',
        'address',
        'types_snapshot'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'is_run' => 'boolean',
        'types_snapshot' => 'json',
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

    /* Accessors */
    public function getSiteNameAttribute()
    {
        return $this->site->name;
    }

    public function getFullNameAttribute()
    {
        return $this->site->name . '-' . $this->attributes['name'] . '-' . $this->attributes['no'];
    }

    /* Mutators */
    public function setSiteNameAttribute($value)
    {
        unset($this->attributes['site_name']);
    }

    public function setFullNameAttribute($value)
    {
        unset($this->attributes['full_name']);
    }

    /* Eloquent Relationships */
    /*public function service_site()
    {
        return $this->belongsTo(ServiceSite::class, 'site_id');
    }*/

    public function site()
    {
        return $this->belongsTo(ServiceSite::class);
    }

    public function type_paper()
    {
        return $this->hasOne(BinTypePaper::class);
    }

    public function type_fabric()
    {
        return $this->hasOne(BinTypeFabric::class);
    }

    public function recyclers()
    {
        return $this->belongsToMany(Recycler::class, 'bin_recyclers', 'bin_id');
    }

    public function token()
    {
        return $this->hasOne(BinToken::class);
    }
}
