<?php

namespace App\Http\Controllers\Client;

use App\Handlers\SocketJsonHandler;
use App\Handlers\Tools\Coordinate;
use App\Http\Requests\Client\BinRequest;
use App\Http\Requests\Client\BinTokenRequest;
use App\Models\Bin;
use App\Models\BinToken;
use App\Models\ClientPrice;
use App\Sockets\BinTcpSocket;
use App\Transformers\Client\BinSimpleTransformer;
use App\Transformers\Client\BinTokenTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class BinsController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/回收箱相关
     * @title GET 获取回收箱列表
     * @method GET
     * @url bins
     * @param lat 必选 string 纬度
     * @param lng 必选 string 经度
     * @param count 非必选(默认10条) string 需要返回的条数
     * @return {"data":[{"id":1,"site_id":1,"name":"呼和浩特上街区","no":"0532001","address":"21 褚 Street","distance":824,"types_snapshot":{"type_paper":{"id":1,"name":"可回收物","unit":"公斤","bin_id":1,"number":"14.88","status":"full","clean_price":{"id":1,"slug":"paper","price":"0.70"},"status_text":"满箱","client_price":{"id":1,"slug":"paper","price":"0.50"},"clean_price_id":1,"client_price_id":1},"type_fabric":{"id":1,"name":"纺织物","unit":"公斤","bin_id":1,"number":"66.54","status":"normal","clean_price":{"id":2,"slug":"fabric","price":"0.40"},"status_text":"正常","client_price":{"id":2,"slug":"fabric","price":"0.10"},"clean_price_id":2,"client_price_id":2}}}],"meta":{"pagination":{"total":25,"count":3,"per_page":3,"current_page":1,"total_pages":9,"links":{"previous":null,"next":"http://bin.test/api/client/bins?lat=36.08743＆lng=120.37479＆page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 回收箱列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 20
     */
    public function index(BinRequest $request)
    {
        $bins = Bin::where('is_run', true)->get();
        $location_coor = new Coordinate($request->get('lat'), $request->get('lng'));


        $bins->transform(function ($bin) use ($location_coor) {
            $bin_coor = new Coordinate($bin->lat, $bin->lng);
            $distance = calcDistance($location_coor, $bin_coor);
            $bin->distance = $distance;
            return $bin;
        });
        $bins = $bins->sortBy('distance');//按距离排序

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

        $item = $bins->forPage($current_page, $perPage); //切分出当前页的数据


        $total = $bins->count();// 查询总数

        $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $paginator->appends($request->except('page'));

        return $this->response->paginator($paginator, new BinSimpleTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/回收箱相关
     * @title GET 获取距离最近的回收箱
     * @method GET
     * @url bins/nearby
     * @param lat 必选 string 纬度
     * @param lng 必选 string 经度
     * @return {"id":1,"site_id":1,"name":"呼和浩特上街区","no":"0532001","address":"21 褚 Street","distance":824,"types_snapshot":{"type_paper":{"id":1,"name":"可回收物","unit":"公斤","bin_id":1,"number":"14.88","status":"full","clean_price":{"id":1,"slug":"paper","price":"0.70"},"status_text":"满箱","client_price":{"id":1,"slug":"paper","price":"0.50"},"clean_price_id":1,"client_price_id":1},"type_fabric":{"id":1,"name":"纺织物","unit":"公斤","bin_id":1,"number":"66.54","status":"normal","clean_price":{"id":2,"slug":"fabric","price":"0.40"},"status_text":"正常","client_price":{"id":2,"slug":"fabric","price":"0.10"},"clean_price_id":2,"client_price_id":2}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data json 回收箱信息
     * @number 10
     */
    public function nearby(BinRequest $request)
    {
        $bins = Bin::where('is_run', true)->get();
        $location_coor = new Coordinate($request->get('lat'), $request->get('lng'));


        $bins->transform(function ($bin) use ($location_coor) {
            $bin_coor = new Coordinate($bin->lat, $bin->lng);
            $distance = calcDistance($location_coor, $bin_coor);
            $bin->distance = $distance;
            return $bin;
        });
        $bins = $bins->sortBy('distance');//按距离排序

        return $this->response->item($bins->first(), new BinSimpleTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/回收箱相关
     * @title PUT 扫码开箱
     * @method PUT
     * @url bins/qrLogin
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param token 必选 string 令牌
     * @return {"id":3,"bin_id":1,"token":"pKrH8FmTPtlu22fC","related_model":null,"related_id":null,"auth_model":"App\\Models\\User","auth_id":1,"created_at":"2019-09-23 09:55:54","updated_at":"2019-09-23 11:13:05"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 令牌信息
     * @number 30
     */
    public function qrLogin(BinTokenRequest $request)
    {
        $token = BinToken::where('token', $request->token)->first();

        if ($token->auth_id != null)
        {
            throw new StoreResourceFailedException(null, [
                'token' => '二维码已使用'
            ]);
        }

        $user = Auth::guard('client')->user();
        $swoole = app('swoole');
        $client_prices = ClientPrice::all();

        $token->related_model = null;
        $token->related_id = null;
        $token->auth_model = $user->getMorphClass();
        $token->auth_id = $user->id;
        $token->save();


        info(json_encode([
            '__action'=> 'user qrLogin',
            '__fd' => $token->fd,
            'static_no' => BinTcpSocket::CLIENT_LOGIN,
            'result_code' => '200',
            'user_card' => (string)$user->id,
            'user_type' => '1', // 1:用户
            'paper_price' => bcmul($client_prices->where('slug', 'paper')->first()['price'], 100),
            'cloth_price' => bcmul($client_prices->where('slug', 'fabric')->first()['price'], 100),
            'money' => bcmul($user->money, 100)
        ]));

        $swoole->send($token->fd, new SocketJsonHandler([
            'static_no' => BinTcpSocket::CLIENT_LOGIN,
            'result_code' => '200',
            'user_card' => (string)$user->id,
            'user_type' => '1', // 1:用户
            'paper_price' => bcmul($client_prices->where('slug', 'paper')->first()['price'], 100),
            'cloth_price' => bcmul($client_prices->where('slug', 'fabric')->first()['price'], 100),
            'money' => bcmul($user->money, 100),
            'paper_money' => 0,
            'cloth _money' => 0,
        ]));


        return $this->response->item($token, new BinTokenTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/回收箱相关
     * @title GET 回收箱订单检查
     * @method GET
     * @url bins/orderCheck/{token_id}
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"id":3,"bin_id":1,"token":"pKrH8FmTPtlu22fC","related_model":"App\\Models\\ClientOrder","related_id":109,"auth_model":"App\\Models\\User","auth_id":1,"created_at":"2019-09-23 09:55:54","updated_at":"2019-09-23 11:13:05"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 令牌信息
     * @number 40
     */
    public function orderCheck(BinToken $token)
    {
        $user = Auth::guard('client')->user();


        return $this->response->item($token, new BinTokenTransformer());
    }
}
