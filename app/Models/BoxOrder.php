<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxOrder extends Model
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
        'box_id',
        'user_id',
        'sn',
        'status',
        'total'
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

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 sn 字段为空
            if (!$model->sn)
            {
                // 调用 generateSn 生成支付序列号
                $model->sn = static::generateSn();
                // 如果生成失败，则终止创建订单
                if (!$model->sn)
                {
                    return false;
                }
            }
        });
    }

    //  生成单号
    public static function generateSn()
    {
        // 单号前缀
        $prefix = 'X' . date('YmdHis');
        for ($i = 0; $i < 10; $i++)
        {
            // 随机生成 6 位的数字
            $sn = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('sn', $sn)->exists())
            {
                return $sn;
            }
        }
        return false;
    }

    /* Accessors */
    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }
}
