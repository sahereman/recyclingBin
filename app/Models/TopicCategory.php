<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCategory extends Model
{
    protected $fillable = [

    ];

    protected $casts = [
    ];

    protected $dates = [
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class, 'category_id');
    }
}
