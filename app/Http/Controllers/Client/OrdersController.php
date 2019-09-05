<?php

namespace App\Http\Controllers\Client;

use App\Transformers\Client\OrderSimpleTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * showdoc
     * @catalog 客户端/订单相关
     * @title GET 获取订单列表
     * @method GET
     * @param Headers.Authorization 必选 headers 用户凭证
     * @url orders
     * @return {"data":[{"id":15,"user_id":1,"status":"completed","status_text":"已完成","bin_name":"济南高坪区","total":"21.54","items":[{"id":23,"order_id":15,"type_name":"纸类、塑料、金属","number":"1.77","unit":"公斤","subtotal":"7.55"}]},{"id":3,"user_id":1,"status":"completed","status_text":"已完成","bin_name":"济南高坪区","total":"28.01","items":[{"id":5,"order_id":3,"type_name":"纸类、塑料、金属","number":"1.07","unit":"公斤","subtotal":"7.55"},{"id":6,"order_id":3,"type_name":"纺织物","number":"2.95","unit":"公斤","subtotal":"3.56"}]}],"meta":{"pagination":{"total":20,"count":5,"per_page":5,"current_page":1,"total_pages":4,"links":{"previous":null,"next":"http://bin.test/api/client/orders?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 订单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index()
    {
        $user = Auth::guard('client')->user();


        $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(5);


        return $this->response->paginator($orders, new OrderSimpleTransformer());

    }
}
