<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWithdraw extends Model
{
    const TYPE_UNION_PAY = 'unionPay';
    const TYPE_WECHAT = 'wechatPay';

    public static $TypeMap = [
        self::TYPE_UNION_PAY => '银联',
        self::TYPE_WECHAT => '微信零钱',
    ];

    const STATUS_WAIT = 'wait';
    const STATUS_AGREE = 'agree';
    const STATUS_DENY = 'deny';

    public static $StatusMap = [
        self::STATUS_WAIT => '待审核',
        self::STATUS_AGREE => '提现成功',
        self::STATUS_DENY => '拒绝提现',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'money',
        'info',
        'reason',
        'checked_at',
        'sn',
        'trace'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'info' => 'json',
        'trace' => 'json',
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'checked_at'
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'type_text',
        'status_text',
    ];

    //  生成单号
    public static function generateSn()
    {
        // 单号前缀
        $prefix = 'W' . date('YmdHis');
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
    public function getTypeTextAttribute()
    {
        return self::$TypeMap[$this->attributes['type']];
    }

    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

    /* Mutators */
    public function setTypeTextAttribute($value)
    {
        unset($this->attributes['type_text']);
    }

    public function setStatusTextAttribute($value)
    {
        unset($this->attributes['status_text']);
    }

    /* Eloquent Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
