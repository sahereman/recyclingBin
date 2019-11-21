<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    const STATUS_NORMAL = 'normal';
    const STATUS_FULL = 'full';
    const STATUS_REPAIR = 'repair';

    public static $StatusMap = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_FULL => '满箱',
        self::STATUS_REPAIR => '维护',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'site_id',
        'status',
        'name',
        'no',
        'lat',
        'lng',
        'address',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
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
        'status_text',
    ];

    /* Accessors */
    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

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

    public function site()
    {
        return $this->belongsTo(ServiceSite::class);
    }

//    public function recyclers()
//    {
//        return $this->belongsToMany(Recycler::class, 'bin_recyclers', 'bin_id');
//    }

//    public function token()
//    {
//        return $this->hasOne(BinToken::class);
//    }
//
//    public function clientOrderItemTemps()
//    {
//        return $this->hasMany(ClientOrderItemTemp::class);
//    }
}
