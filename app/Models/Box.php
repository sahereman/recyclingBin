<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    const STATUS_NORMAL = 'normal';
    const STATUS_FULL = 'full';
    const STATUS_REPAIR = 'repair';

    public static $StatusMap = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_FULL => '满箱',
        self::STATUS_REPAIR => '维护',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'site_id',
        'status',
        'name',
        'no',
        'lat',
        'lng',
        'address',
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
        static::created(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no)
            {
                // 调用 generateSn 生成箱体号
                $model->no = static::generateNo($model);
                $model->save();
            }
        });
    }

    //  生成箱体号
    public static function generateNo($model)
    {
        // 号前缀
        $prefix = 'CM';
        for ($i = 0; $i < 10; $i++)
        {
            $no = $prefix . str_pad($model->id, 7, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists())
            {
                return $no;
            }
        }
        return null;
    }

    /* Accessors */
    public function getStatusTextAttribute()
    {
        return self::$StatusMap[$this->attributes['status']];
    }

    public function getSiteNameAttribute()
    {
        return $this->site->name;
    }

    public function getFullNameAttribute()
    {
        return $this->site->name . '-' . $this->attributes['name'] . '-' . $this->attributes['no'];
    }

    /* Mutators */
    public function setSiteNameAttribute($value)
    {
        unset($this->attributes['site_name']);
    }

    public function setFullNameAttribute($value)
    {
        unset($this->attributes['full_name']);
    }

    public function site()
    {
        return $this->belongsTo(ServiceSite::class);
    }

    //    public function recyclers()
    //    {
    //        return $this->belongsToMany(Recycler::class, 'bin_recyclers', 'bin_id');
    //    }

    //    public function token()
    //    {
    //        return $this->hasOne(BinToken::class);
    //    }
    //
    //    public function clientOrderItemTemps()
    //    {
    //        return $this->hasMany(ClientOrderItemTemp::class);
    //    }
}
