<?php

namespace App\Http\Controllers\Client;

use App\Handlers\Tools\Coordinate;
use App\Http\Requests\Client\BoxRequest;
use App\Models\Box;
use App\Models\Config;
use App\Transformers\Client\BoxSimpleTransformer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class BoxesController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/传统箱相关
     * @title GET 获取传统箱列表
     * @method GET
     * @url boxes
     * @param lat 必选 string 纬度
     * @param lng 必选 string 经度
     * @param count 非必选(默认10条) string 需要返回的条数
     * @return {"data":[{"id":7,"site_id":1,"name":"拉萨大东区","no":"CM0532007","address":"92 僧 Street","distance":113,"lat":"36.086550","lng":"120.375420"},{"id":6,"site_id":1,"name":"南京大东区","no":"CM0532006","address":"10 纪 Street","distance":147,"lat":"36.087550","lng":"120.376420"}],"meta":{"pagination":{"total":25,"count":10,"per_page":10,"current_page":1,"total_pages":3,"links":{"previous":null,"next":"http://bin.test/api/client/boxes?lat=36.08743＆lng=120.37479＆page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 传统箱列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index(BoxRequest $request)
    {
        $boxes = Box::all();
        $location_coor = new Coordinate($request->get('lat'), $request->get('lng'));

        $boxes->transform(function ($box) use ($location_coor) {
            $box_coor = new Coordinate($box->lat, $box->lng);
            $distance = calcDistance($location_coor, $box_coor);
            $box->distance = $distance;
            return $box;
        });
        $boxes = $boxes->sortBy('distance');//按距离排序

        $perPage = $request->get('count', 10);// 每页显示数量
        if ($request->has('page'))
        {
            // 请求是第几页，如果没有传page数据，则默认为1
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 : $current_page;
        } else
        {
            $current_page = 1;
        }

        $item = $boxes->forPage($current_page, $perPage); //切分出当前页的数据


        $total = $boxes->count();// 查询总数

        $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $paginator->appends($request->except('page'));

        return $this->response->paginator($paginator, new BoxSimpleTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/传统箱相关
     * @title GET 获取距离最近的传统箱
     * @method GET
     * @url boxes/nearby
     * @param lat 必选 string 纬度
     * @param lng 必选 string 经度
     * @return {"id":7,"site_id":1,"name":"拉萨大东区","no":"CM0532007","address":"92 僧 Street","distance":113,"lat":"36.086550","lng":"120.375420"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data json 传统箱信息
     * @number 10
     */
    public function nearby(BoxRequest $request)
    {
        $boxes = Box::all();
        $location_coor = new Coordinate($request->get('lat'), $request->get('lng'));

        $boxes->transform(function ($box) use ($location_coor) {
            $box_coor = new Coordinate($box->lat, $box->lng);
            $distance = calcDistance($location_coor, $box_coor);
            $box->distance = $distance;
            return $box;
        });
        $boxes = $boxes->sortBy('distance');//按距离排序

        return $this->response->item($boxes->first(), new BoxSimpleTransformer());
    }


    /**
     * showdoc
     * @catalog 客户端/传统箱相关
     * @title GET 获取传统箱奖励参数
     * @method GET
     * @url boxes/profits
     * @return {"box_order_profit_day":"7","box_order_profit_number":"2","box_order_profit_money":"0.2"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data json 奖励参数信息
     * @number 10
     */
    public function profits()
    {
        $box_order_profit_day = Config::config('box_order_profit_day');
        $box_order_profit_number = Config::config('box_order_profit_number');
        $box_order_profit_money = Config::config('box_order_profit_money');

        return $this->response->array([
            'box_order_profit_day' => $box_order_profit_day,
            'box_order_profit_number' => $box_order_profit_number,
            'box_order_profit_money' => $box_order_profit_money,
        ]);

    }

}
