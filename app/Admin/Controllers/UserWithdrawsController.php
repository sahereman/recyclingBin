<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Ajax\Ajax_Button;
use App\Admin\Extensions\Ajax\Ajax_Input_Text_Button;
use App\Exceptions\InvalidRequestException;
use App\Models\UserMoneyBill;
use App\Models\UserWithdraw;
use App\Notifications\Client\UserWithdrawAgreeNotification;
use App\Notifications\Client\UserWithdrawDenyNotification;
use Dingo\Api\Exception\StoreResourceFailedException;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserWithdrawsController extends AdminController
{
    protected $title = '用户提现申请';

    protected function grid()
    {
        $grid = new Grid(new UserWithdraw);
        $grid->model()->orderBy('status', 'desc')->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*禁用*/
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableBatchActions();
        $grid->disableExport();

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });
                }, '用户');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('phone', 'like', "%{$this->input}%");
                    });
                }, '手机号');
                $filter->between('created_at', '申请时间')->datetime();
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->equal('status','状态')->select(UserWithdraw::$StatusMap);
                $filter->equal('type','到账方式')->select(UserWithdraw::$TypeMap);
                $filter->between('checked_at', '审核时间')->datetime();
            });
        });

        $grid->user('用户')->display(function ($user) {
            return "<a href='" . route('admin.users.show', $user['id']) . "'>$user[name]</a>";
        });
        $grid->created_at('申请时间');
        $grid->type_text('到账方式');
        $grid->status_text('状态');
        $grid->money('金额');
        $grid->info('提现预留信息')->display(function ($info) {
            $str = '';
            switch ($this->type)
            {
                case UserWithdraw::TYPE_UNION_PAY:
                    $str = "户名:$info[name]<br/>账号:$info[account]<br/>银行:$info[bank]<br/>开户行:$info[bank_name]<br/>";
                    break;
            }
            return $str;
        });
        $grid->checked_at('审核时间');
        $grid->reason('拒绝原因');
        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            if ($this->status == UserWithdraw::STATUS_WAIT)
            {
                $buttons .= new Ajax_Button(route('admin.user_withdraws.agree', $this->id), [], '同意');
                $buttons .= new Ajax_Input_Text_Button(route('admin.user_withdraws.deny', $this->id), [], '拒绝', '请输入拒绝原因');
            }
            return $buttons;
        });

        return $grid;
    }

    public function agree(Request $request, UserWithdraw $withdraw)
    {
        if ($withdraw->status !== UserWithdraw::STATUS_WAIT)
        {
            return response()->json([
                'status' => false,
                'message' => '状态异常'
            ]);
        }

        // 提现成功,修改用户冻结金额,修改提现状态,通知用户,改变账单
        $user = $withdraw->user;
        $withdraw->update([
            'status' => UserWithdraw::STATUS_AGREE,
            'checked_at' => now(),
        ]);
        $user->update([
            'frozen_money' => bcsub($user->frozen_money, $withdraw->money, 2),
        ]);
        UserMoneyBill::change($user, UserMoneyBill::TYPE_USER_WITHDRAW, $withdraw->money, $withdraw);
        $withdraw->user->notify(new UserWithdrawAgreeNotification($withdraw));


        return response()->json([
            'status' => true,
            'message' => '提现成功'
        ]);
    }

    public function deny(Request $request, UserWithdraw $withdraw)
    {
        if ($withdraw->status !== UserWithdraw::STATUS_WAIT)
        {
            return response()->json([
                'status' => false,
                'message' => '状态异常'
            ]);
        }

        // 验证
        $data = Validator::make($request->all(), [
            'input' => ['required'],
        ], [], [
            'input' => '拒绝原因',
        ])->validate();

        // 拒绝提现,修改用户冻结金额|余额,修改提现状态|拒绝原因,通知用户
        $user = $withdraw->user;
        $withdraw->update([
            'status' => UserWithdraw::STATUS_DENY,
            'checked_at' => now(),
            'reason' => $data['input']
        ]);
        $user->update([
            'money' => bcadd($user->money, $withdraw->money, 2),
            'frozen_money' => bcsub($user->frozen_money, $withdraw->money, 2),
        ]);
        $withdraw->user->notify(new UserWithdrawDenyNotification($withdraw));


        return response()->json([
            'status' => true,
            'message' => '拒绝提现'
        ]);

    }
}
