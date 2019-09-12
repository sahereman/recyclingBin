<?php

namespace App\Http\Controllers\Clean;

use App\Transformers\Clean\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * showdoc
     * @catalog 回收端/订单相关
     * @title GET 获取订单列表
     * @method GET
     * @url orders
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":9,"recycler_id":1,"status":"completed","status_text":"已完成","bin_name":"哈尔滨翔安区","total":"97.92","items":[{"id":15,"order_id":9,"type_name":"可回收物","number":"1.42","unit":"公斤","subtotal":"4.18"}]},{"id":10,"recycler_id":1,"status":"completed","status_text":"已完成","bin_name":"海口淄川区","total":"32.04","items":[{"id":16,"order_id":10,"type_name":"纺织物","number":"2.75","unit":"公斤","subtotal":"2.59"},{"id":17,"order_id":10,"type_name":"纺织物","number":"1.06","unit":"公斤","subtotal":"4.61"}]}],"meta":{"pagination":{"total":20,"count":5,"per_page":5,"current_page":1,"total_pages":4,"links":{"previous":null,"next":"http://bin.test/api/recycle/orders?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 订单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index()
    {
        $recycler = Auth::guard('clean')->user();


        $orders = $recycler->orders()->orderBy('created_at', 'desc')->paginate(5);


        return $this->response->paginator($orders, new OrderTransformer());

    }
}
