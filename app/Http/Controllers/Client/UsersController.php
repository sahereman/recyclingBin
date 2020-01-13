<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\BindPhoneRequest;
use App\Http\Requests\Client\UserRequest;
use App\Http\Requests\Client\WithdrawUnionPayRequest;
use App\Http\Requests\Client\WithdrawWechatPayRequest;
use App\Http\Requests\Request;
use App\Jobs\UserWithdrawWechatPay;
use App\Models\ClientOrder;
use App\Models\User;
use App\Models\UserMoneyBill;
use App\Models\UserWithdraw;
use App\Transformers\Client\NotificationTransformer;
use App\Transformers\Client\UserMoneyBillTransformer;
use App\Transformers\Client\UserTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\RateLimitExceededException;
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
     * @return {"id":1,"wx_openid":"jWhoCXr0YOGemt4d","name":"华坤","gender":"女","phone":"18600982820","avatar_url":"http://bin.test/defaults/user_avatar.png","money":"901.94","notification_count":33,"total_client_order_money":"922.97","total_client_order_count":23,"real_authenticated_at":null,"real_name":"","real_id":"","created_at":"2019-10-09 01:01:59","updated_at":"2019-10-15 10:01:04"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 用户信息
     * @number 60
     */
    public function show()
    {
        $user = Auth::guard('client')->user();

        if ($user != null)
        {
            return $this->response->item($user, new UserTransformer());
        } else
        {
            info($user);
            throw new RateLimitExceededException();
        }

    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title GET 获取消息通知
     * @method GET
     * @url users/notifications
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":[{"title":"投递成功","info":"恭喜您投递成功,获得奖励金96.22元,前往\"我的订单\"页面查看投递详情","relation_model":"App\\Models\\ClientOrder","relation_id":12,"link":"","created_at":"2019-09-16 11:18:01"},{"title":"投递成功","info":"恭喜您投递成功,获得奖励金76.45元,前往\"我的订单\"页面查看投递详情","relation_model":"App\\Models\\ClientOrder","relation_id":8,"link":"","created_at":"2019-09-16 11:18:01"},{"title":"提现失败","info":"很抱歉您申请的提现未通过审核,提现金额36.99元,失败原因:银行预留信息错误,请修改后重新提交申请","relation_model":"App\\Models\\UserWithdraw","relation_id":6,"link":"","created_at":"2019-09-16 11:17:59"},{"title":"提现失败","info":"很抱歉您申请的提现未通过审核,提现金额28.33元,失败原因:银行预留信息错误,请修改后重新提交申请","relation_model":"App\\Models\\UserWithdraw","relation_id":5,"link":"","created_at":"2019-09-16 11:17:59"},{"title":"提现成功","info":"恭喜您提现成功,提现金额75.83元已转入银行卡,银行账号:62223078323174632,请注意查收","relation_model":"App\\Models\\UserWithdraw","relation_id":7,"link":"","created_at":"2019-09-16 11:17:59"}],"meta":{"pagination":{"total":8,"count":5,"per_page":5,"current_page":1,"total_pages":2,"links":{"previous":null,"next":"http://bin.test/api/client/users/notifications?page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 消息通知数据
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 65
     */
    public function notifications()
    {
        $user = Auth::guard('client')->user();
        $notifications = $user->notifications()->paginate(10);

        $user->markAsRead();

        return $this->response->paginator($notifications, new NotificationTransformer());
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
     * @return {"id":1,"wx_openid":"7GSjvYxzNoVgluIK","name":"鄢金凤","gender":"男","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?99944","money":"677.36","total_client_order_money":"626.03","total_client_order_count":"66.00","real_authenticated_at":null,"real_name":"","real_id":"","created_at":"2019-09-12 04:46:34","updated_at":"2019-09-12 09:40:12"}
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

        $unauthorized_user = User::where('phone', $verify_data['phone'])->where('wx_openid', null)->first();

        // 如果有相同手机的未授权用户,将未授权用户数据合并,删除未授权用户
        if ($unauthorized_user)
        {
            // 同步订单
            $unauthorized_user->orders->each(function ($order) use ($user) {
                $order->user()->associate($user);
                $order->save();
            });

            // 同步账单
            $unauthorized_user->moneyBills->each(function ($bill) use ($user) {
                $bill->user()->associate($user);
                $bill->save();
            });

            // 合并用户信息
            $user->update([
                'money' => bcadd($user->money, $unauthorized_user->money, 2),
                'total_client_order_money' => bcadd($user->total_client_order_money, $unauthorized_user->total_client_order_money, 2),
                'total_client_order_count' => bcadd($user->total_client_order_count, $unauthorized_user->total_client_order_count),
                'total_client_order_number' => bcadd($user->total_client_order_number, $unauthorized_user->total_client_order_number, 2),
            ]);

            $unauthorized_user->delete();
        }

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
     * @param type 非必选(参考值:all,clientOrder,other) string 账单类型
     * @param date 非必选(参考值:2019-08) string 状态
     * @return {"data":[{"id":23,"user_id":1,"type":"clientOrder","type_text":"回收订单","description":"投递废品","operator":"+","number":"28.09","created_at":"2019-09-27 16:45:58"},{"id":24,"user_id":1,"type":"clientOrder","type_text":"回收订单","description":"投递废品","operator":"+","number":"55.09","created_at":"2019-09-27 16:45:58"},{"id":25,"user_id":1,"type":"clientOrder","type_text":"回收订单","description":"投递废品","operator":"+","number":"55.68","created_at":"2019-09-27 16:45:58"}],"meta":{"pagination":{"total":18,"count":3,"per_page":3,"current_page":1,"total_pages":6,"links":{"previous":null,"next":"http://bin.test/api/client/users/moneyBill?type=clientOrder＆date=2019-09＆page=2"}}}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json 账单列表信息
     * @return_param mata.pagination json 分页信息 (使用links.next前往下一页数据)
     * @number 70
     */
    public function moneyBill(Request $request)
    {
        $user = Auth::guard('client')->user();

        $builder = $user->moneyBills()->orderBy('created_at', 'desc');

        // 筛选类型
        switch ($request->input('type'))
        {
            case 'all':
                break;
            case 'clientOrder' :
                $builder->where('type', UserMoneyBill::TYPE_CLIENT_ORDER);
                break;
            case 'other' :
                $builder->where('type', '!=', UserMoneyBill::TYPE_CLIENT_ORDER);
                break;
        }

        // 筛选时间
        try
        {
            $date = Carbon::createFromFormat('Y-m', $request->input('date'));

        } catch (\InvalidArgumentException $e)
        {
            $date = null;
        }
        if ($date instanceof Carbon)
        {
            $builder->whereBetween('created_at', [
                $date->startOfMonth()->toDateTimeString(),// start
                $date->endOfMonth()->toDateTimeString(),// end
            ]);
        }

        $bills = $builder->paginate(10)->appends($request->except('page'));

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
     * @param bank_name 必选 string 开户行
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
                    'bank_name' => $request->input('bank_name')
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
     * @title POST 用户微信钱包提现
     * @method POST
     * @url users/withdraw/wechatPay
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param money 必选 string 金额
     * @return []
     * @return_param HTTP.Status int 成功时HTTP状态码:201
     * @number 80
     * @throws \Exception
     */
    public function WithdrawWechatPay(WithdrawWechatPayRequest $request)
    {
        $user = User::find(Auth::guard('client')->user()->id);

        if ($user->money < $request->input('money'))
        {
            throw new StoreResourceFailedException(null, [
                'money' => '余额不足'
            ]);
        }

        $withdraw = DB::transaction(function () use ($user, $request) {

            $withdraw = UserWithdraw::create([
                'user_id' => $user->id,
                'type' => UserWithdraw::TYPE_WECHAT,
                'status' => UserWithdraw::STATUS_WAIT,
                'money' => $request->input('money'),
                'info' => [],
                'sn' => UserWithdraw::generateSn(),
            ]);

            $user->frozen_money = bcadd($user->frozen_money, $withdraw->money, 2);
            $user->money = bcsub($user->money, $withdraw->money, 2);
            $user->save();

            return $withdraw;
        });

        UserWithdrawWechatPay::dispatch($withdraw);

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
