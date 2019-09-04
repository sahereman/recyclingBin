<?php

namespace App\Admin\Models;

use App\Models\Topic as TopicModel;

class Topic extends TopicModel
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'category_name',
        'thumb_url',
        'image_url',
        'content_simple',
    ];
}
