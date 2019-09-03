<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    const SLUG_MINI = 'mini-index';

    public static $SlugMap = [
        self::SLUG_MINI => '小程序首页',
    ];

    protected $fillable = [

    ];

    protected $casts = [
    ];

    protected $dates = [

    ];


    public $timestamps = false;

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://']))
        {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
