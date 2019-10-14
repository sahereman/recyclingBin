<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class RecyclerPayment extends Model
{

//    const METHOD_ALIPAY = 'alipay';
    const METHOD_WECHAT = 'wechat';

    public static $paymentMethodMap = [
//        self::METHOD_ALIPAY => '支付宝',
        self::METHOD_WECHAT => '微信',
    ];

    protected $fillable = [
        'sn',
        'recycler_id',
        'amount',
        'method',
        'payment_sn',
        'paid_at',
    ];

    protected $casts = [
        //
    ];

    protected $dates = [
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 sn 字段为空
            if (!$model->sn) {
                // 调用 generateSn 生成支付序列号
                $model->sn = static::generateSn();
                // 如果生成失败，则终止创建订单
                if (!$model->sn) {
                    return false;
                }
            }
        });
    }

    //  生成支付序列号
    public static function generateSn()
    {
        // 支付序列号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $sn = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('sn', $sn)->exists()) {
                return $sn;
            }
        }
        Log::error('Generating Sn Failed');
        return false;
    }

    public function recycler()
    {
        return $this->belongsTo(Recycler::class);
    }
}
