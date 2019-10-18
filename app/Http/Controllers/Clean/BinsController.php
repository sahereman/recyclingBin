<?php

namespace App\Http\Controllers\Clean;

use App\Handlers\SocketJsonHandler;
use App\Http\Requests\Client\BinTokenRequest;
use App\Models\BinToken;
use App\Models\CleanPrice;
use App\Sockets\BinTcpSocket;
use App\Transformers\Client\BinTokenTransformer;
use Illuminate\Support\Facades\Auth;

class BinsController extends Controller
{

    /**
     * showdoc
     * @catalog 回收端/回收箱相关
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

        //        if ($token->auth_id != null)
        //        {
        //            throw new StoreResourceFailedException(null, [
        //                'token' => '令牌已使用,请重新获取'
        //            ]);
        //        }

        $recycler = Auth::guard('clean')->user();
        $swoole = app('swoole');
        $clean_prices = CleanPrice::all();

        $token->auth_model = $recycler->getMorphClass();
        $token->auth_id = $recycler->id;
        $token->save();

        info(json_encode([
            '__action'=> 'recycler qrLogin',
            'static_no' => BinTcpSocket::CLIENT_LOGIN,
            'result_code' => '200',
            'user_card' => (string)$recycler->id,
            'user_type' => '2', // 2:回收员
            'paper_price' => bcmul($clean_prices->where('slug', 'paper')->first()['price'], 100),
            'cloth_price' => bcmul($clean_prices->where('slug', 'fabric')->first()['price'], 100),
            'money' => bcmul($recycler->money, 100)
        ]));

        $swoole->send($token->fd, new SocketJsonHandler([
            'static_no' => BinTcpSocket::CLIENT_LOGIN,
            'result_code' => '200',
            'user_card' => (string)$recycler->id,
            'user_type' => '2', // 2:回收员
            'paper_price' => bcmul($clean_prices->where('slug', 'paper')->first()['price'], 100),
            'cloth_price' => bcmul($clean_prices->where('slug', 'fabric')->first()['price'], 100),
            'money' => bcmul($recycler->money, 100)
        ]));


        return $this->response->item($token, new BinTokenTransformer());
    }

}
