<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\UserWithdraw;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        //        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '昵称');
                $filter->like('phone', '手机号');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('money', '奖励金');
                $filter->where(function ($query) {
                    switch ($this->input)
                    {
                        case 'yes':
                            $query->where('real_authenticated_at', '!=', null);
                            break;
                        case 'no':
                            $query->where('real_authenticated_at', null);
                            break;
                    }
                }, '已实名')->radio([
                    'all' => '不选择',
                    'yes' => '是',
                    'no' => '否',
                ]);
            });
        });

        $grid->column('id', 'ID')->sortable();
        $grid->avatar('头像')->image('', 40);
        $grid->column('name', '昵称');
        $grid->column('gender', '性别')->sortable();
        $grid->column('phone', '手机号');
        // $grid->column('avatar', 'Avatar');
        $grid->column('money', '奖励金')->sortable();
        // $grid->column('frozen_money', '冻结金额')->sortable();
        //        $grid->column('total_client_order_money', '累计投递订单金额')->sortable();
        $grid->column('total_client_order_count', '累计投递订单次数')->sortable();
        // $grid->column('wx_openid', '微信授权 ID');
        // $grid->column('wx_country', '微信 Country');
        //        $grid->column('wx_province', '微信 Province');
        //        $grid->column('wx_city', '微信 City');
        //        $grid->column('real_id', '身份证号');
        //        $grid->column('real_name', '真实姓名');
        // $grid->column('real_authenticated_at', '实名认证时间');
        // $grid->column('notification_count', '通知未读数');
        //        $grid->column('email', 'Email');
        // $grid->column('email_verified_at', 'Email verified at');
        // $grid->column('password', 'Password');
        $grid->column('created_at', '创建时间')->sortable();
        // $grid->column('updated_at', 'Updated at');

        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
        // $grid->disableCreation(); // Deprecated
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;

        $show->field('id', 'ID');
        $show->field('avatar', '头像')->image('', 120);
        $show->field('name', '昵称');
        $show->field('gender', '性别');
        $show->field('phone', '手机号');
        $show->field('money', '奖励金');
        $show->field('frozen_money', '冻结金额');
        $show->field('total_client_order_money', '累计投递订单金额');
        $show->field('total_client_order_count', '累计投递订单次数');
        $show->field('wx_openid', '微信 OpenId');
        $show->field('wx_country', '微信 Country');
        $show->field('wx_province', '微信 Province');
        $show->field('wx_city', '微信 City');
        $show->field('real_id', '身份证号');
        $show->field('real_name', '真实姓名');
        $show->field('real_authenticated_at', '实名认证时间');
        //        $show->field('email', 'Email');
        //        $show->field('email_verified_at', 'Email verified at');
        // $show->field('password', 'Password');
        // $show->field('created_at', 'Created at');
        // $show->field('updated_at', 'Updated at');

        $show->moneyBills('账单记录', function ($moneyBill) {
            $moneyBill->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $moneyBill->disableCreateButton();
            $moneyBill->disableFilter();
            $moneyBill->disableActions();
            $moneyBill->disableBatchActions();

            // grid
            $moneyBill->created_at('时间');
            $moneyBill->type_text('类型');
            $moneyBill->description('描述');
            $moneyBill->operator_number('金额');
        });

        $show->withdraws('提现记录', function ($withdraw) {
            $withdraw->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $withdraw->disableCreateButton();
            $withdraw->disableFilter();
            $withdraw->disableActions();
            $withdraw->disableBatchActions();

            // grid
            $withdraw->created_at('时间');
            $withdraw->type_text('到账方式');
            $withdraw->status_text('状态');
            $withdraw->money('金额');
            $withdraw->info('提现预留信息')->display(function ($info) {
                $str = '';
                switch ($this->type)
                {
                    case UserWithdraw::TYPE_UNION_PAY:
                        $str = "户名:$info[name]<br/>账号:$info[account]<br/>银行:$info[bank]<br/>开户行:$info[bank_name]<br/>";
                        break;
                }
                return $str;
            });
            $withdraw->reason('拒绝原因');
        });

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->display('id', 'ID');
        $form->image('avatar', '头像')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        $form->text('name', '昵称')->rules('required|string');
        $form->radio('gender', '性别')->options(['男' => '男', '女' => '女'])->default('男')->rules('required|in:男,女');
        $form->mobile('phone', '手机号');
        $form->display('money', '奖励金');
        $form->display('frozen_money', '冻结金额');
        $form->display('total_client_order_money', '累计投递订单金额');
        $form->display('total_client_order_count', '累计投递订单次数');
        $form->display('wx_openid', '微信 openid');
        $form->display('wx_country', '微信 country');
        $form->display('wx_province', '微信 province');
        $form->display('wx_city', '微信 city');
        $form->display('real_id', '身份证号');
        $form->display('real_name', '真实姓名');
        $form->display('real_authenticated_at', '实名认证时间');
//        $form->switch('is_authenticated', '已实名')->states([
//            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
//            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
//        ]);
        // $form->email('email', 'Email');
        //        $form->display('email', 'Email');
        // $form->datetime('email_verified_at', 'Email verified at'))->default(date('Y-m-d H:i:s');
        // $form->password('password', 'Password');

        return $form;
    }
}
