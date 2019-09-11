<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecyclerDeposit extends Model
{
    const METHOD_WECHAT = 'wechat';

    public static $MethodMap = [
        self::METHOD_WECHAT => '微信',
    ];


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
        'sn',
        'method',
        'money',
        'paid_at',
        'method',
        'payment_sn',
        'paid_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'paid_at' => 'date'
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
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

    //  生成充值单号
    public static function generateSn()
    {
        // 充值单号前缀
        $prefix = date('YmdHis');
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

    public function getMethodTextAttribute()
    {
        return self::$MethodMap[$this->attributes['method']];
    }

    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }
}
