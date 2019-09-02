<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinTypeFabric extends Model
{
    const NAME = '纺织物';

    const STATUS_NORMAL = 'normal';
    const STATUS_FULL = 'full';
    const STATUS_REPAIR = 'repair';

    public static $StatusMap = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_FULL => '满箱',
        self::STATUS_REPAIR => '维护',
    ];

    protected $fillable = [

    ];

    protected $casts = [
    ];

    protected $dates = [

    ];

    protected $appends = [
        'status_text'
    ];

    public $timestamps = false;

    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

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
