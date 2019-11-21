<?php

namespace App\Http\Controllers\Client;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Client\BoxOrderRequest;
use App\Models\Box;
use App\Models\BoxOrder;
use App\Models\Config;
use App\Transformers\Client\BoxOrderTransformer;
use Illuminate\Support\Facades\Auth;

class BoxOrdersController extends Controller
{
    /**
     * showdoc
     * @catalog 客户端/传统箱订单相关
     * @title GET 获取传统箱订单列表
     * @method GET
     * @url box_orders
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":3,"user_id":1,"status":"completed","status_text":"已完成","image_proof_url":"https://lorempixel.com/640/480/?86747","total":"0.20","created_at":"2019-11-20 22:12:07"},{"id":5,"user_id":1,"status":"completed","status_text":"已完成","image_proof_url":"https://lorempixel.com/640/480/?91776","total":"0.20","created_at":"2019-11-20 20:00:29"}],"meta":{"pagination":{"total":20,"count":10,"per_page":10,"current_page":1,"total_pages":2,"links":{"previous":null,"next":"http://bin.test/api/client/box_orders?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 订单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index()
    {
        $user = Auth::guard('client')->user();


        $orders = $user->box_orders()->orderBy('created_at', 'desc')->paginate(10);


        return $this->response->paginator($orders, new BoxOrderTransformer());

    }

    /**
     * showdoc
     * @catalog 客户端/传统箱订单相关
     * @title GET 获取传统箱订单详情
     * @method GET
     * @url box_orders/{order_id}
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"id":2,"user_id":1,"status":"completed","status_text":"已完成","image_proof_url":"https://lorempixel.com/640/480/?84092","total":"0.00","created_at":"2019-11-17 18:06:50"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 订单信息
     * @number 30
     */
    public function show(BoxOrder $order)
    {
        $user = Auth::guard('client')->user();
        if (!$user->can('own', $order))
        {
            return $this->response->errorForbidden('This action is unauthorized.');
        }

        return $this->response->item($order, new BoxOrderTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/传统箱相关
     * @title POST 传统箱投递
     * @method POST
     * @url box_orders
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param box_no 必选 string 传统箱编号
     * @param image_proof 必选 string 图片凭证(图片上传接口中返回的path)
     * @return {"id":48,"user_id":1,"status":"completed","status_text":"已完成","image_proof_url":"http://bin.test/storage/original/201911/ETAF2UeA8P8JbnUl1NCdwnkAmrwAEX6VNMFiNzYo.jpeg","total":"0.2","created_at":"2019-11-21 13:53:13"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 订单信息
     * @number 30
     */
    public function store(BoxOrderRequest $request)
    {
        $user = Auth::guard('client')->user();
        $box = Box::where('no', $request->input('box_no'))->first();

        $box_order_profit_day = Config::config('box_order_profit_day');
        $box_order_profit_number = Config::config('box_order_profit_number');
        $box_order_profit_money = Config::config('box_order_profit_money');

        $his_orders = BoxOrder::where('user_id', $user->id)->where('total', '>', 0)->whereBetween('created_at', [
            now()->subMinutes($box_order_profit_day),// start
            now(),// end
        ])->get();

        $order = new BoxOrder();
        $order->box()->associate($box);
        $order->user()->associate($user);
        $order->status = BoxOrder::STATUS_COMPLETED;
        $order->image_proof = $request->input('image_proof');
        $order->total = $his_orders->count() < $box_order_profit_number ? $box_order_profit_money : 0;
        $order->save();

        return $this->response->item($order, new BoxOrderTransformer());
    }
}
