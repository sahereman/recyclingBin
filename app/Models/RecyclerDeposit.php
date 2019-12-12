<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecyclerDeposit extends Model
{
    const STATUS_PAYING = 'paying';
    const STATUS_COMPLETED = 'completed';

    public static $StatusMap = [
        self::STATUS_PAYING => '支付中',
        self::STATUS_COMPLETED => '支付完成',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'recycler_id',
        'payment_id',
        'sn',
        'money',
        'status',
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
    ];

    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

    public function payment()
    {
        return $this->belongsTo(RecyclerPayment::class);
    }

    public function recycler()
    {
        return $this->belongsTo(Recycler::class);
    }
}
