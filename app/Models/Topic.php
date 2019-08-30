<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    protected $fillable = [

    ];

    protected $casts = [
        'is_index' => 'boolean',
    ];

    protected $dates = [
    ];

    protected $appends = ['thumb_url', 'image_url'];

    public function getThumbUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['thumb'], ['http://', 'https://']))
        {
            return $this->attributes['thumb'];
        }
        return \Storage::disk('public')->url($this->attributes['thumb']);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://']))
        {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function getContentSimpleAttribute()
    {
        //从HTML中截取纯文本字符串
        $str = trim(html_entity_decode(strip_tags($this->attributes['content'])));
        if (strlen($str) > 50)
        {
            return substr($str, 0, 47) . '...';
        } else
        {
            return $str;
        }
    }
}
