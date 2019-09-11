<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinTypePaper extends Model
{
    const NAME = '可回收物';

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
     *
     * @var array
     */
    protected $fillable = [
        'bin_id',
        'name',
        'status',
        'number',
        'unit',
        'client_price_id',
        'recycle_price_id',
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_text',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /* Accessors */
    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

    public function getClientPriceValueAttribute()
    {
        return $this->client_price->price;
    }

    public function getRecyclePriceValueAttribute()
    {
        return $this->recycle_price->price;
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

    public function setRecyclePriceValueAttribute($value)
    {
        unset($this->attributes['recycle_price_value']);
    }

    /* Eloquent Relationships */
    public function client_price()
    {
        return $this->belongsTo(ClientPrice::class);
    }

    public function recycle_price()
    {
        return $this->belongsTo(RecyclePrice::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
