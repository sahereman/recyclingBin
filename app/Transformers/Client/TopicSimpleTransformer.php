<?php

namespace App\Transformers\Client;

use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class TopicSimpleTransformer extends TransformerAbstract
{
    public function transform(Topic $topic)
    {
        return [
            'id' => $topic->id,
            'category_id' => $topic->category_id,
            'is_index' => $topic->is_index,
            'title' => $topic->title,
            'thumb_url' => $topic->thumb_url,
            'image_url' => $topic->image_url,
            'content_simple' => $topic->content_simple,
            'view_count' => $topic->view_count,

            'created_at' => $topic->created_at->toDateTimeString(),
            'updated_at' => $topic->updated_at->toDateTimeString(),
        ];
    }


}