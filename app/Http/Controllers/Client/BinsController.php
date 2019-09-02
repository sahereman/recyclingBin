<?php

namespace App\Http\Controllers\Client;

use App\Handlers\Tools\Coordinate;
use App\Http\Requests\Client\BinsIndexRequest;
use App\Models\Bin;
use App\Transformers\Client\BinSimpleTransformer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BinsController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/回收箱相关
     * @title GET 获取回收箱列表
     * @method GET
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param lat 必选 string 纬度
     * @param lng 必选 string 经度
     * @url bins
     * @return {"data":[{"id":1,"site_id":1,"name":"香港兴山区","no":"0532001","address":"10 张 Street","distance":824,"types_snapshot":{"type_paper":{"id":1,"name":"纸类、塑料、金属","unit":"公斤","bin_id":1,"number":"67.12","status":"full","status_text":"满箱","client_price":{"id":1,"slug":"paper","price":"0.50"},"recycle_price":{"id":1,"slug":"paper","price":"0.70"},"client_price_id":1,"recycle_price_id":1},"type_fabric":{"id":1,"name":"纺织物","unit":"公斤","bin_id":1,"number":"86.43","status":"normal","status_text":"正常","client_price":{"id":2,"slug":"fabric","price":"0.10"},"recycle_price":{"id":2,"slug":"fabric","price":"0.40"},"client_price_id":2,"recycle_price_id":2}}}],"meta":{"pagination":{"total":25,"count":3,"per_page":3,"current_page":1,"total_pages":9,"links":{"previous":null,"next":"http://bin.test/api/client/bins?lat=36.08743＆lng=120.37479＆page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 回收箱列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index(BinsIndexRequest $request)
    {
        $bins = Bin::all();
        $location_coor = new Coordinate($request->get('lat'), $request->get('lng'));


        $bins->transform(function ($bin) use ($location_coor) {
            $bin_coor = new Coordinate($bin->lat, $bin->lng);
            $distance = calcDistance($location_coor, $bin_coor);
            $bin->distance = $distance;
            return $bin;
        });
        $bins = $bins->sortBy('distance');//按距离排序

        $perPage = 3;// 每页显示数量
        if ($request->has('page'))
        {
            // 请求是第几页，如果没有传page数据，则默认为1
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 : $current_page;
        } else
        {
            $current_page = 1;
        }

        $item = $bins->forPage(($current_page - 1) * $perPage, $perPage); //切分出当前页的数据


        $total = $bins->count();// 查询总数

        $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $paginator->appends($request->except('page'));

        return $this->response->paginator($paginator, new BinSimpleTransformer());
    }
}
