<?php

namespace App\Http\Controllers\Clean;

use App\Models\CleanOrder;
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
     * @return {"data":[{"id":10,"recycler_id":1,"sn":"R20191009105659749350","status":"completed","status_text":"已完成","bin_name":"西安白云区","total":"89.94","created_at":"2019-10-09 05:45:32","items":[{"id":14,"order_id":10,"type_slug":"fabric","type_name":"纺织物","number":"0.14","unit":"公斤","subtotal":"4.21"}]},{"id":11,"recycler_id":1,"sn":"R20191009105659828393","status":"completed","status_text":"已完成","bin_name":"西安白云区","total":"71.11","created_at":"2019-10-08 00:02:00","items":[{"id":15,"order_id":11,"type_slug":"fabric","type_name":"纺织物","number":"1.35","unit":"公斤","subtotal":"5.62"},{"id":16,"order_id":11,"type_slug":"paper","type_name":"可回收物","number":"2.37","unit":"公斤","subtotal":"8.08"}]},{"id":19,"recycler_id":1,"sn":"R20191009105659979660","status":"completed","status_text":"已完成","bin_name":"长沙浔阳区","total":"62.04","created_at":"2019-10-07 19:49:35","items":[{"id":29,"order_id":19,"type_slug":"paper","type_name":"可回收物","number":"2.61","unit":"公斤","subtotal":"3.78"}]},{"id":20,"recycler_id":1,"sn":"R20191009105659251735","status":"completed","status_text":"已完成","bin_name":"西安白云区","total":"72.40","created_at":"2019-10-07 13:52:57","items":[{"id":30,"order_id":20,"type_slug":"paper","type_name":"可回收物","number":"1.77","unit":"公斤","subtotal":"7.38"},{"id":31,"order_id":20,"type_slug":"fabric","type_name":"纺织物","number":"1.69","unit":"公斤","subtotal":"3.18"}]},{"id":15,"recycler_id":1,"sn":"R20191009105659663473","status":"completed","status_text":"已完成","bin_name":"西安白云区","total":"69.29","created_at":"2019-10-07 06:07:00","items":[{"id":23,"order_id":15,"type_slug":"fabric","type_name":"纺织物","number":"2.83","unit":"公斤","subtotal":"2.81"}]}],"meta":{"pagination":{"total":20,"count":5,"per_page":5,"current_page":1,"total_pages":4,"links":{"previous":null,"next":"http://bin.test/api/clean/orders?page=2"}}}}
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

    /**
     * showdoc
     * @catalog 回收端/订单相关
     * @title GET 获取订单详情
     * @method GET
     * @url orders/{order_id}
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"id":1,"recycler_id":1,"sn":"R20191009105658543250","status":"completed","status_text":"已完成","bin_name":"济南新城区","total":"33.46","created_at":"2019-10-06 04:42:47","items":[{"id":1,"order_id":1,"type_slug":"fabric","type_name":"纺织物","number":"0.59","unit":"公斤","subtotal":"3"}]}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 订单信息
     * @number 30
     */
    public function show(CleanOrder $order)
    {
        $recycler = Auth::guard('clean')->user();

        if (!$recycler->can('own', $order))
        {
            return $this->response->errorForbidden('This action is unauthorized.');
        }

        return $this->response->item($order, new OrderTransformer());
    }
}
