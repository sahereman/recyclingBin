<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinTypeFabric extends Model
{
    const NAME = '纺织物';
    const SLUG = 'fabric';

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
        'bin_id',
        'name',
        'status',
        'number',
        'unit',
        'threshold',
        'client_price_id',
        'clean_price_id',
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
        'status_text',
    ];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /* Accessors */
    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

    public function getThresholdAttribute()
    {
        return $this->attributes['threshold'] ?: Config::config('fabric_threshold');
    }

    public function getClientPriceValueAttribute()
    {
        return $this->client_price->price;
    }

    public function getCleanPriceValueAttribute()
    {
        return $this->clean_price->price;
    }

    /* Mutators */
    public function setStatusTextAttribute($value)
    {
        unset($this->attributes['status_text']);
    }

    public function setClientPriceValueAttribute($value)
    {
        unset($this->attributes['client_price_value']);
    }

    public function setCleanPriceValueAttribute($value)
    {
        unset($this->attributes['clean_price_value']);
    }

    /* Eloquent Relationships */
    public function client_price()
    {
        return $this->belongsTo(ClientPrice::class);
    }

    public function clean_price()
    {
        return $this->belongsTo(CleanPrice::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
