<?php

namespace App\Http\Controllers\Clean;


use App\Http\Requests\Clean\PasswordResetRequest;
use App\Http\Requests\Clean\WithdrawUnionPayRequest;
use App\Models\Recycler;
use App\Models\RecyclerWithdraw;
use App\Transformers\Clean\BinTransformer;
use App\Transformers\Clean\RecyclerMoneyBillTransformer;
use App\Transformers\Clean\RecyclerTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class RecyclersController extends Controller
{


    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title GET 获取回收员信息
     * @method GET
     * @url recyclers/show
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"id":1,"name":"原彬","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?36391","money":"145.95","created_at":"2019-09-08 08:52:46","updated_at":"2019-09-09 10:58:46"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 用户信息
     * @number 60
     */
    public function show()
    {
        $recycler = Auth::guard('clean')->user();

        return $this->response->item($recycler, new RecyclerTransformer());
    }

    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title PUT 回收员重置密码
     * @method PUT
     * @url recyclers/passwordReset
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param phone 必选 string 手机号
     * @param verification_key 必选 string 短信验证码key
     * @param verification_code 必选 string 短信验证码
     * @param password 必选 string 密码
     * @param password_confirmation 必选 string 确认密码
     * @return ["密码重置成功,请重新登录"]
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @number 50
     */
    public function passwordReset(PasswordResetRequest $request)
    {
        $verify_data = Cache::get($request->verification_key);
        // Cache::forget($request->verification_key);// 清除验证码缓存

        if (!$verify_data || !hash_equals($verify_data['code'], $request->verification_code))
        {
            throw new StoreResourceFailedException(null, [
                'verification_code' => '验证码错误'
            ]);
        }

        $recycler = Auth::guard('clean')->user();
        $recycler->password = bcrypt($request->password);
        $recycler->save();

        Auth::guard('clean')->logout();

        return $this->response->array([
            '密码重置成功,请重新登录'
        ]);
    }


    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title GET 获取金钱账单列表
     * @method GET
     * @url recyclers/moneyBill
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":1,"user_id":null,"type":"recyclerDeposit","type_text":"回收员充值","description":"余额充值","operator":"+","number":"7.00","created_at":"2019-09-12 09:40:12"},{"id":2,"user_id":null,"type":"recyclerDeposit","type_text":"回收员充值","description":"余额充值","operator":"+","number":"11.00","created_at":"2019-09-12 09:40:12"}],"meta":{"pagination":{"total":27,"count":5,"per_page":5,"current_page":1,"total_pages":6,"links":{"previous":null,"next":"http://bin.test/api/clean/recyclers/moneyBill?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 账单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 70
     */
    public function moneyBill()
    {
        $recycler = Auth::guard('clean')->user();

        $bills = $recycler->moneyBills()->paginate(5);

        return $this->response->paginator($bills, new RecyclerMoneyBillTransformer());
    }

    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title POST 回收员银联提现
     * @method POST
     * @url recyclers/withdraw/unionPay
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param name 必选 string 持卡人姓名
     * @param bank 必选 string 银行
     * @param account 必选 string 账号
     * @param money 必选 string 金额
     * @return []
     * @return_param HTTP.Status int 成功时HTTP状态码:201
     * @number 80
     * @throws \Exception
     */
    public function WithdrawUnionPay(WithdrawUnionPayRequest $request)
    {
        $recycler = Recycler::find(Auth::guard('clean')->user()->id);

        if ($recycler->money < $request->input('money'))
        {
            throw new StoreResourceFailedException(null, [
                'money' => '余额不足'
            ]);
        }

        DB::transaction(function () use ($recycler, $request) {

            $withdraw = RecyclerWithdraw::create([
                'recycler_id' => $recycler->id,
                'type' => RecyclerWithdraw::TYPE_UNION_PAY,
                'status' => RecyclerWithdraw::STATUS_WAIT,
                'money' => $request->input('money'),
                'info' => [
                    'name' => $request->input('name'),
                    'bank' => $request->input('bank'),
                    'account' => $request->input('account'),
                ]
            ]);

            $recycler->frozen_money = bcadd($recycler->frozen_money, $withdraw->money, 2);
            $recycler->money = bcsub($recycler->money, $withdraw->money, 2);
            $recycler->save();
        });

        return $this->response->created();
    }

    /**
     * showdoc
     * @catalog 回收端/回收箱相关
     * @title GET 获取我的回收箱
     * @method GET
     * @url recyclers/bins
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":1,"site_id":1,"name":"呼和浩特上街区","no":"0532001","address":"21 褚 Street","site":{"id":1,"name":"青岛站","county":"中国","province":"山东省","province_simple":"山东","city":"青岛市","city_simple":"青岛","created_at":"2019-09-12 09:40:13","updated_at":"2019-09-12 09:40:13"},"type_paper":{"id":1,"bin_id":1,"name":"可回收物","status":"full","number":"14.88","unit":"公斤","client_price_id":1,"clean_price_id":1,"status_text":"满箱","clean_price":{"id":1,"slug":"paper","price":"0.70"}},"type_fabric":{"id":1,"bin_id":1,"name":"纺织物","status":"normal","number":"66.54","unit":"公斤","client_price_id":2,"clean_price_id":2,"status_text":"正常","clean_price":{"id":2,"slug":"fabric","price":"0.40"}}},{"id":2,"site_id":1,"name":"天津西夏区","no":"0532002","address":"74 胥 Street","site":{"id":1,"name":"青岛站","county":"中国","province":"山东省","province_simple":"山东","city":"青岛市","city_simple":"青岛","created_at":"2019-09-12 09:40:13","updated_at":"2019-09-12 09:40:13"},"type_paper":{"id":2,"bin_id":2,"name":"可回收物","status":"full","number":"58.56","unit":"公斤","client_price_id":1,"clean_price_id":1,"status_text":"满箱","clean_price":{"id":1,"slug":"paper","price":"0.70"}},"type_fabric":{"id":2,"bin_id":2,"name":"纺织物","status":"full","number":"88.66","unit":"公斤","client_price_id":2,"clean_price_id":2,"status_text":"满箱","clean_price":{"id":2,"slug":"fabric","price":"0.40"}}}]}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @number 80
     */
    public function bins()
    {
        $recycler = Auth::guard('clean')->user();

        $bins = $recycler->bins()->with(['site', 'type_paper', 'type_fabric', 'type_paper.clean_price', 'type_fabric.clean_price'])->get();

        return $this->response->collection($bins, new BinTransformer());

    }
}
