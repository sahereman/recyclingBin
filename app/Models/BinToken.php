<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BinToken extends Model
{
    protected $fillable = [
        'bin_id',
        'related_model',
        'related_id',
        'token',

    ];

    protected $casts = [
        //
    ];

    protected $dates = [
        //
    ];

    protected $appends = [
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 sn 字段为空
            if (!$model->token)
            {
                // 调用 generateSn 生成支付序列号
                $model->token = static::generateToken();
                // 如果生成失败，则终止创建订单
                if (!$model->token)
                {
                    return false;
                }
            }
        });
    }

    public static function generateToken()
    {
        for ($i = 0; $i < 10; $i++)
        {
            $token = Str::random(16);

            // 判断是否已经存在
            if (!static::query()->where('token', $token)->exists())
            {
                return $token;
            }
        }
        return false;
    }


    public function related()
    {
        return $this->belongsTo($this->related_model, 'related_id');
    }

    public function auth()
    {
        return $this->belongsTo($this->auth_model, 'auth_id');
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

}
