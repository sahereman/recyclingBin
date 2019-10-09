<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable
    {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::guard('client')->id())
        {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'gender',
        'phone',
        'avatar',
        'money',
        'frozen_money',
        'wx_country',
        'wx_province',
        'wx_city',
        'wx_openid',
        'wx_session_key',
        'real_name',
        'real_id',
        'real_authenticated_at',
        'email',
        'email_verified_at',
        'password',
        'disabled_at',

        'total_client_order_money',
        'total_client_order_count',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'real_authenticated_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'avatar_url',
    ];

    /* Accessors */
    public function getAvatarUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['avatar'], ['http://', 'https://']))
        {
            return $this->attributes['avatar'];
        }
        return \Storage::disk('public')->url($this->attributes['avatar']);
    }

    /* Mutators */
    public function setAvatarUrlAttribute($value)
    {
        unset($this->attributes['avatar_url']);
    }

    /* Eloquent Relationships */
    public function orders()
    {
        return $this->hasMany(ClientOrder::class);
    }

    public function moneyBills()
    {
        return $this->hasMany(UserMoneyBill::class);
    }

    public function withdraws()
    {
        return $this->hasMany(UserWithdraw::class);
    }
}
