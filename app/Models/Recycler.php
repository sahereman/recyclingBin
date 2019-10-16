<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;


class Recycler extends Authenticatable implements JWTSubject
{
    use Notifiable
    {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::guard('clean')->id())
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'frozen_money',
        'money',
        'phone',
        'name',
        'notification_count',
        'disabled_at',
        'avatar',
        'contract_start_time',
        'contract_end_time',
        'password',

        'wx_openid',
        'wx_session_key',
    ];

    protected $hidden = [
    ];

    protected $casts = [
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $dates = [
        'contract_start_time',
        'contract_end_time',
    ];

    /* Accessors */
    public function getAvatarUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['avatar'], ['http://', 'https://']))
        {
            return $this->attributes['avatar'];
        }
        return Storage::disk('public')->url($this->attributes['avatar']);
    }

    /* Eloquent Relationships */
    public function bins()
    {
        return $this->belongsToMany(Bin::class, 'bin_recyclers', 'recycler_id');
    }

    public function orders()
    {
        return $this->hasMany(CleanOrder::class);
    }

    public function moneyBills()
    {
        return $this->hasMany(RecyclerMoneyBill::class);
    }

    public function withdraws()
    {
        return $this->hasMany(RecyclerWithdraw::class);
    }
}
