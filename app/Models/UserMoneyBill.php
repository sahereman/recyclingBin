<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMoneyBill extends Model
{
    const TYPE_CLIENT_ORDER = 'clientOrder';
    const TYPE_USER_WITHDRAW = 'userWithdraw';

    public static $TypeMap = [
        self::TYPE_CLIENT_ORDER => '回收订单',
        self::TYPE_USER_WITHDRAW => '用户提现',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'operator',
        'number',
        'related_model',
        'related_id'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        //
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
        'type_text',
        'operator_number'
    ];

    /* Accessors */
    public function getTypeTextAttribute()
    {
        return self::$TypeMap[$this->attributes['type']];
    }

    public function getOperatorNumberAttribute()
    {
        return $this->attributes['operator'] . ' ' . $this->attributes['number'];
    }

    /* Mutators */
    public function setTypeTextAttribute($value)
    {
        unset($this->attributes['type_text']);
    }

    public function setOperatorNumberAttribute($value)
    {
        unset($this->attributes['operator_number']);
    }

    /* Eloquent Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->belongsTo($this->related_model, 'related_id');
    }

    public static function change(User $user, $type, $number, Model $related = null)
    {
        if (!in_array($type, array_keys(self::$TypeMap))) {
            throw new \Exception('账单类型异常');
        }

        if ($number < 0) {
            throw new \Exception('账单数额异常');
        }

        switch ($type) {
            case self::TYPE_CLIENT_ORDER :
                if (!$related instanceof ClientOrder || !$related->exists) {
                    throw new \Exception('关联模型异常');
                }
                $operator = '+';
                $description = '投递废品';
                break;
            case self::TYPE_USER_WITHDRAW :
                if (!$related instanceof UserWithdraw || !$related->exists) {
                    throw new \Exception('关联模型异常');
                }
                $operator = '-';
                $description = '奖励金提现';
                break;
        }

        $data = [
            'user_id' => $user->id,
            'type' => $type,
            'description' => $description,
            'operator' => $operator,
            'number' => $number,
        ];

        if ($related != null && $related->exists) {
            $data = array_merge($data, [
                'related_model' => $related->getMorphClass(),
                'related_id' => $related->id,
            ]);
        }

        self::create($data);
    }
}
