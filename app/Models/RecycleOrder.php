<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleOrder extends Model
{
    const STATUS_COMPLETED = 'completed';

    public static $StatusMap = [
        self::STATUS_COMPLETED => '已完成',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'recycler_id',
        'status',
        'bin_snapshot',
        'total'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'bin_snapshot' => 'json',
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

    /* Mutators */
    public function setStatusTextAttribute($value)
    {
        unset($this->attributes['status_text']);
    }

    /* Eloquent Relationships */
    public function items()
    {
        return $this->hasMany(RecycleOrder::class, 'order_id');
    }

    public function recycler()
    {
        return $this->belongsTo(Recycler::class);
    }
}
