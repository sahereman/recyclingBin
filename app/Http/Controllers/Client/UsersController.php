<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\BindPhoneRequest;
use App\Http\Requests\Client\UserRequest;
use App\Http\Requests\Client\WithdrawUnionPayRequest;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Transformers\Client\UserMoneyBillTransformer;
use App\Transformers\Client\UserTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller
{


    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title GET 获取用户信息
     * @method GET
     * @url users/show
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":{"id":1,"wx_openid":"kZeqcFTG2xgJ0yM9","name":"伍玉兰","gender":"女","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?95254","money":"0.00","created_at":"2019-08-24 19:37:26","updated_at":"2019-08-29 09:56:56"}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 用户信息
     * @number 60
     */
    public function show()
    {
        $user = Auth::guard('client')->user();

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title PUT 用户绑定手机
     * @method PUT
     * @url users/bindPhone
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param phone 必选 string 手机号
     * @param verification_key 必选 string 短信验证码key
     * @param verification_code 必选 string 短信验证码
     * @return {"id":1,"wx_openid":"BNfbhkx3mB8BryGN","name":"牟志新","gender":"男","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?96741","money":"522.14","created_at":"2019-09-03 03:57:16","updated_at":"2019-09-06 11:46:12"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @number 50
     */
    public function bindPhone(BindPhoneRequest $request)
    {
        $verify_data = Cache::get($request->verification_key);
        // Cache::forget($request->verification_key);// 清除验证码缓存

        if (!$verify_data || !hash_equals($verify_data['code'], $request->verification_code))
        {
            throw new StoreResourceFailedException(null, [
                'verification_code' => '验证码错误'
            ]);
        }

        $user = Auth::guard('client')->user();

        $user->phone = $verify_data['phone'];
        $user->save();

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title GET 获取金钱账单列表
     * @method GET
     * @url users/moneyBill
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"id":1,"user_id":1,"type":"clientOrder","type_text":"回收订单","description":"投递废品","operator":"+","number":"24.02","created_at":"2019-09-05 15:23:14"},{"id":2,"user_id":1,"type":"clientOrder","type_text":"回收订单","description":"投递废品","operator":"+","number":"90.43","created_at":"2019-09-05 15:23:14"}],"meta":{"pagination":{"total":20,"count":5,"per_page":5,"current_page":1,"total_pages":4,"links":{"previous":null,"next":"http://bin.test/api/client/users/moneyBill?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 账单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 70
     */
    public function moneyBill()
    {
        $user = Auth::guard('client')->user();


        $bills = $user->moneyBills()->paginate(5);

        return $this->response->paginator($bills, new UserMoneyBillTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title POST 用户银联提现
     * @method POST
     * @url users/withdraw/unionPay
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
        $user = User::find(Auth::guard('client')->user()->id);

        if ($user->money < $request->input('money'))
        {
            throw new StoreResourceFailedException(null, [
                'money' => '余额不足'
            ]);
        }


        DB::transaction(function () use ($user, $request) {

            $withdraw = UserWithdraw::create([
                'user_id' => $user->id,
                'type' => UserWithdraw::TYPE_UNION_PAY,
                'status' => UserWithdraw::STATUS_WAIT,
                'money' => $request->input('money'),
                'info' => [
                    'name' => $request->input('name'),
                    'bank' => $request->input('bank'),
                    'account' => $request->input('account'),
                ]
            ]);

            $user->frozen_money = bcadd($user->frozen_money, $withdraw->money, 2);
            $user->money = bcsub($user->money, $withdraw->money, 2);
            $user->save();
        });

        return $this->response->created();
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title PUT 用户实名认证
     * @method PUT
     * @url users/real_authentication
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param real_name 必选 string 真实姓名
     * @param real_id 必选 string 身份证号码
     * @return {"id":1,"wx_openid":"Tc9qpK2GhLspffwP","name":"屈飞","gender":"女","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?64462","money":"103.45","real_authenticated_at":"2019-09-09 09:55:51","real_name":"dsad","real_id":"732878328727827832","created_at":"2019-09-05 13:13:12","updated_at":"2019-09-09 09:55:51"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @number 75
     * @throws \Exception
     */
    public function realAuthentication(UserRequest $request)
    {
        $user = Auth::guard('client')->user();

        if ($user->real_authenticated_at)
        {
            throw new StoreResourceFailedException(null, [
                'real_name' => '用户已实名认证,无法继续'
            ]);
        }

        $attributes = $request->only(['real_name', 'real_id']);
        $attributes['real_authenticated_at'] = Carbon::now();


        $user->update($attributes);
        return $this->response->item($user, new UserTransformer());
    }
}
