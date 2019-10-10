<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'thumb',
        'image',
        'content',
        'is_index',
        'view_count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_index' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        //
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'category_name',
        'thumb_url',
        'image_url',
        // 'content_simple',
    ];

    /* Accessors */
    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }

    public function getThumbUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['thumb'], ['http://', 'https://'])) {
            return $this->attributes['thumb'];
        }
        return \Storage::disk('public')->url($this->attributes['thumb']);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function getContentSimpleAttribute()
    {
        //从 HTML 中截取纯文本字符串
        $str = trim(html_entity_decode(strip_tags($this->attributes['content'])));
        if (strlen($str) > 50) {
            return substr($str, 0, 47) . '...';
        } else {
            return $str;
        }
    }

    /* Mutators */
    public function setCategoryNameAttribute($value)
    {
        unset($this->attributes['category_name']);
    }

    public function setThumbUrlAttribute($value)
    {
        unset($this->attributes['thumb_url']);
    }

    public function setImageUrlAttribute($value)
    {
        unset($this->attributes['image_url']);
    }

    public function setContentSimpleAttribute($value)
    {
        unset($this->attributes['content_simple']);
    }

    /* Eloquent Relationships */
    public function category()
    {
        return $this->belongsTo(TopicCategory::class);
    }
}
