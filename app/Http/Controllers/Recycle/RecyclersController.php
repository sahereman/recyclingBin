<?php

namespace App\Http\Controllers\Recycle;


use App\Http\Requests\Client\WithdrawUnionPayRequest;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Transformers\Recycle\RecyclerTransformer;
use App\Transformers\Client\UserMoneyBillTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class RecyclersController extends Controller
{


    /**
     * showdoc
     * @catalog 回收端/用户相关
     * @title GET 获取用户信息
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
        $user = Auth::guard('recycle')->user();

        return $this->response->item($user, new RecyclerTransformer());
    }


    /**
     * @catalog 回收端/用户相关
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
     * @catalog 回收端/用户相关
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
}
