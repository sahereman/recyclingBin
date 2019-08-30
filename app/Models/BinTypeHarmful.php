<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinTypeHarmful extends Model
{
    const NAME = '有害物品';

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

    public $timestamps = false;
}
