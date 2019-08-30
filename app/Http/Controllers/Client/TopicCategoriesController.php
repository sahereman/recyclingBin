<?php

namespace App\Http\Controllers\Client;

use App\Models\TopicCategory;
use App\Transformers\Client\TopicCategoryTransformer;
use App\Transformers\Client\TopicSimpleTransformer;
use App\Transformers\Client\TopicTransformer;
use Illuminate\Http\Request;

class TopicCategoriesController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/话题相关
     * @title GET 获取话题分类
     * @method GET
     * @param Headers.Authorization 必选 headers 用户凭证
     * @url topic_categories
     * @return {"data":[{"id":2,"name":"亮海绿","sort":225},{"id":3,"name":"蓝色","sort":396},{"id":1,"name":"紫水晶色","sort":430},{"id":4,"name":"海绿","sort":640}]}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 话题分类信息
     * @number 10
     */
    public function index()
    {
        $topic_categories = TopicCategory::orderBy('sort')->get();

        return $this->response->collection($topic_categories, new TopicCategoryTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/话题相关
     * @title GET 获取话题列表
     * @method GET
     * @param Headers.Authorization 必选 headers 用户凭证
     * @url topic_categories/{category_id}
     * @return {"data":[{"id":28,"category_id":2,"is_index":true,"title":"天益传媒有限公司","thumb_url":"https://lorempixel.com/200/480/?52176","image_url":"https://lorempixel.com/640/480/?67929","content_simple":"Quidem nisi lab   ore animi numquam rem.quas...","created_at":"2019-08-26 16:36:37","updated_at":"2019-08-25 17:18:50"},{"id":39,"category_id":2,"is_index":false,"title":"泰麒麟网络有限公司","thumb_url":"https://lorempixel.com/200/480/?89725","image_url":"https://lorempixel.com/640/480/?85591","content_simple":"Tenetur modi at quia dolore adipisci.repellatex","created_at":"2019-08-30 10:11:54","updated_at":"2019-08-28 20:51:29"},{"id":35,"category_id":2,"is_index":false,"title":"彩虹科技有限公司","thumb_url":"https://lorempixel.com/200/480/?65671","image_url":"https://lorempixel.com/640/480/?76234","content_simple":"Occaecati et aut aliquam at qui laboriosam.mole...","created_at":"2019-08-29 23:55:20","updated_at":"2019-08-26 13:39:42"}],"meta":{"pagination":{"total":20,"count":5,"per_page":5,"current_page":1,"total_pages":4,"links":{"previous":null,"next":"http://bin.test/api/client/topic_categories/2?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 话题列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function topic(TopicCategory $category)
    {
        $topics = $category->topics()->orderByDesc('is_index')->orderByDesc('created_at')->paginate(5);

        return $this->response->paginator($topics, new TopicSimpleTransformer());

    }

}
