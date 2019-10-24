<?php

namespace App\Http\Controllers\Clean;

use App\Models\CleanPrice;
use App\Transformers\Clean\CleanPriceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleanPricesController extends Controller
{
    /**
     * showdoc
     * @catalog 回收端/回收箱相关
     * @title GET 获取回收价格列表
     * @method GET
     * @url cleanPrices
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":1,"slug":"paper","price":"0.70","unit":"公斤"},{"id":2,"slug":"fabric","price":"0.40","unit":"公斤"}]}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 价格列表信息
     * @number 90
     */
    public function index()
    {
        $recycler = Auth::guard('clean')->user();

        $prices = CleanPrice::all();

        return $this->response->collection($prices, new CleanPriceTransformer());
    }
}
