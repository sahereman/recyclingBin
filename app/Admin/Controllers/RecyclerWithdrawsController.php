<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Ajax\Ajax_Button;
use App\Admin\Extensions\Ajax\Ajax_Input_Text_Button;
use App\Models\RecyclerMoneyBill;
use App\Models\RecyclerWithdraw;
use App\Notifications\Clean\RecyclerWithdrawAgreeNotification;
use App\Notifications\Clean\RecyclerWithdrawDenyNotification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RecyclerWithdrawsController extends AdminController
{
    protected $title = '回收员提现申请';

    protected function grid()
    {
        $grid = new Grid(new RecyclerWithdraw);
        $grid->model()->orderBy('status', 'desc')->orderBy('created_at', 'desc'); // 设置初始排序条件
        $grid->disableExport();

        /*禁用*/
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableBatchActions();

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereHas('recycler', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });
                }, '回收员');
                $filter->where(function ($query) {
                    $query->whereHas('recycler', function ($query) {
                        $query->where('phone', 'like', "%{$this->input}%");
                    });
                }, '手机号');
                $filter->between('created_at', '申请时间')->datetime();
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->equal('status','状态')->select(RecyclerWithdraw::$StatusMap);
                $filter->equal('type','到账方式')->select(RecyclerWithdraw::$TypeMap);
                $filter->between('checked_at', '审核时间')->datetime();
            });
        });

        $grid->recycler('回收员')->display(function ($recycler) {
            return "<a href='" . route('admin.recyclers.show', $recycler['id']) . "'>$recycler[name]</a>";
        });
        $grid->created_at('申请时间');
        $grid->type_text('到账方式');
        $grid->status_text('状态');
        $grid->money('金额');
        $grid->info('提现预留信息')->display(function ($info) {
            $str = '';
            switch ($this->type)
            {
                case RecyclerWithdraw::TYPE_UNION_PAY:
                    $str = "户名:$info[name]<br/>账号:$info[account]<br/>银行:$info[bank]<br/>开户行:$info[bank_name]<br/>";
                    break;
            }
            return $str;
        });
        $grid->checked_at('审核时间');
        $grid->reason('拒绝原因');
        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            if ($this->status == RecyclerWithdraw::STATUS_WAIT)
            {
                $buttons .= new Ajax_Button(route('admin.recycler_withdraws.agree', $this->id), [], '同意');
                $buttons .= new Ajax_Input_Text_Button(route('admin.recycler_withdraws.deny', $this->id), [], '拒绝', '请输入拒绝原因');
            }
            return $buttons;
        });

        return $grid;
    }

    public function agree(Request $request, RecyclerWithdraw $withdraw)
    {
        if ($withdraw->status !== RecyclerWithdraw::STATUS_WAIT)
        {
            return response()->json([
                'status' => false,
                'message' => '状态异常'
            ]);
        }

        // 提现成功,修改用户冻结金额,修改提现状态,通知回收员,改变账单
        $recycler = $withdraw->recycler;
        $withdraw->update([
            'status' => RecyclerWithdraw::STATUS_AGREE,
            'checked_at' => now(),
        ]);
        $recycler->update([
            'frozen_money' => bcsub($recycler->frozen_money, $withdraw->money, 2),
        ]);
        RecyclerMoneyBill::change($recycler,RecyclerMoneyBill::TYPE_RECYCLER_WITHDRAW,$withdraw->money,$withdraw);
        $withdraw->recycler->notify(new RecyclerWithdrawAgreeNotification($withdraw));


        return response()->json([
            'status' => true,
            'message' => '提现成功'
        ]);
    }

    public function deny(Request $request, RecyclerWithdraw $withdraw)
    {
        if ($withdraw->status !== RecyclerWithdraw::STATUS_WAIT)
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
        $recycler = $withdraw->recycler;
        $withdraw->update([
            'status' => RecyclerWithdraw::STATUS_DENY,
            'checked_at' => now(),
            'reason' => $data['input']
        ]);
        $recycler->update([
            'money' => bcadd($recycler->money, $withdraw->money, 2),
            'frozen_money' => bcsub($recycler->frozen_money, $withdraw->money, 2),
        ]);
        $withdraw->recycler->notify(new RecyclerWithdrawDenyNotification($withdraw));


        return response()->json([
            'status' => true,
            'message' => '拒绝提现'
        ]);

    }
}
