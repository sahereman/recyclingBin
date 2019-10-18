<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecyclerMoneyBill extends Model
{
    const TYPE_CLEAN_ORDER = 'cleanOrder';
    const TYPE_RECYCLER_WITHDRAW = 'recyclerWithdraw';
    const TYPE_RECYCLER_DEPOSIT = 'recyclerDeposit';

    public static $TypeMap = [
        self::TYPE_CLEAN_ORDER => '取货订单',
        self::TYPE_RECYCLER_WITHDRAW => '回收员提现',
        self::TYPE_RECYCLER_DEPOSIT => '回收员充值',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'recycler_id',
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
    public function recycler()
    {
        return $this->belongsTo(Recycler::class);
    }

    public function related()
    {
        return $this->belongsTo($this->related_model, 'related_id');
    }

    public static function change(Recycler $recycler, $type, $number, Model $related = null)
    {
        if (!in_array($type, array_keys(self::$TypeMap))) {
            throw new \Exception('账单类型异常');
        }

        if ($number < 0) {
            throw new \Exception('账单数额异常');
        }

        switch ($type) {
            case self::TYPE_CLEAN_ORDER :
                if (!$related instanceof CleanOrder || !$related->exists) {
                    throw new \Exception('关联模型异常');
                }
                $operator = '-';
                $description = '回收废品';
                break;
            case self::TYPE_RECYCLER_WITHDRAW :
                if (!$related instanceof RecyclerWithdraw || !$related->exists) {
                    throw new \Exception('关联模型异常');
                }
                $operator = '-';
                $description = '余额提现';
                break;
            case self::TYPE_RECYCLER_DEPOSIT :
                if (!$related instanceof RecyclerDeposit || !$related->exists) {
                    throw new \Exception('关联模型异常');
                }
                $operator = '+';
                $description = '余额充值';
                break;
        }

        $data = [
            'recycler_id' => $recycler->id,
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
