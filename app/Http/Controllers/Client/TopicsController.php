<?php

namespace App\Http\Controllers\Client;

use App\Models\Topic;
use App\Transformers\Client\TopicTransformer;

class TopicsController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/话题相关
     * @title GET 获取话题详情
     * @method GET
     * @url topics/{topic_id}
     * @return {"id":1,"category_id":1,"is_index":true,"title":"四通信息有限公司","thumb_url":"https://lorempixel.com/200/480/?81246","image_url":"https://lorempixel.com/640/480/?93189","content":"<html><head><title>Itaque delectus aspernatur voluptatum necessitatibus.</title></head><body><form action=\"example.net\" method=\"POST\"><label for=\"username\">vero</label><input type=\"text\" id=\"username\"><label for=\"password\">assumenda</label><input type=\"password\" id=\"password\"></form><div class=\"quod\"><div class=\"repellat\"></div><div id=\"18047\"></div></div></body></html>\n","created_at":"2019-08-26 17:56:35","updated_at":"2019-08-27 04:03:57"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param id int 话题id
     * @return_param category_id int 话题分类id
     * @return_param is_index boolean 是否推荐
     * @return_param title string 标题
     * @return_param thumb_url string 缩略图url
     * @return_param image_url string 图片url
     * @return_param content string 内容
     * @return_param created_at string 创建时间
     * @return_param updated_at string 更新时间
     * @number 30
     */
    public function show(Topic $topic)
    {
        $topic->incrment('view_count');
        $topic->save();
        return $this->response->item($topic, new TopicTransformer());
    }

}
