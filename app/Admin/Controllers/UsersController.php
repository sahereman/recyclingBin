<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('name', '用户名');
        });

        $grid->column('id', 'Id')->sortable();
        $grid->avatar('Avatar')->image('', 40);
        $grid->column('name', 'Name');
        $grid->column('gender', 'Gender')->sortable();
        $grid->column('phone', 'Phone');
        // $grid->column('avatar', 'Avatar');
        $grid->column('money', '奖励金')->sortable();
        // $grid->column('frozen_money', '冻结金额')->sortable();
        $grid->column('total_client_order_money', '累计投递订单金额')->sortable();
        $grid->column('total_client_order_count', '累计投递订单次数')->sortable();
        // $grid->column('wx_openid', '微信授权 ID');
        // $grid->column('wx_country', '微信 Country');
        $grid->column('wx_province', '微信 Province');
        $grid->column('wx_city', '微信 City');
        $grid->column('real_id', '身份证号');
        $grid->column('real_name', '真实姓名');
        $grid->column('is_authenticated', '是否实名验证')->switch([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);
        // $grid->column('real_authenticated_at', '实名认证时间');
        // $grid->column('notification_count', '通知未读数');
        $grid->column('email', 'Email');
        // $grid->column('email_verified_at', 'Email verified at');
        // $grid->column('password', 'Password');
        // $grid->column('created_at', 'Created at');
        // $grid->column('updated_at', 'Updated at');

        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
        // $grid->disableCreation(); // Deprecated
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;

        $show->field('id', 'Id');
        $show->field('avatar', 'Avatar')->image('', 120);
        $show->field('name', 'Name');
        $show->field('gender', 'Gender');
        $show->field('phone', 'Phone');
        // $show->field('avatar', 'Avatar');
        $show->field('money', '奖励金');
        $show->field('frozen_money', '冻结金额');
        $show->field('total_client_order_money', '累计投递订单金额');
        $show->field('total_client_order_count', '累计投递订单次数');
        // $show->field('wx_openid', '微信授权 ID');
        $show->field('wx_country', '微信 Country');
        $show->field('wx_province', '微信 Province');
        $show->field('wx_city', '微信 City');
        $show->field('real_id', '身份证号');
        $show->field('real_name', '真实姓名');
        $show->field('is_authenticated', '是否实名验证')->as(function ($is_authenticated) {
            return $is_authenticated ? 'YES' : 'NO';
        });
        $show->field('email', 'Email');
        $show->field('email_verified_at', 'Email verified at');
        // $show->field('password', 'Password');
        // $show->field('created_at', 'Created at');
        // $show->field('updated_at', 'Updated at');

        $show->moneyBills('账单记录', function ($moneyBill) {
            $moneyBill->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            // $moneyBill->disableCreation(); // Deprecated
            $moneyBill->disableCreateButton();

            // 禁用筛选
            $moneyBill->disableFilter();

            // 禁用导出数据按钮
            // $moneyBill->disableExport();

            /*自定义筛选框*/
            /*$moneyBill->filter(function ($filter) {
                $filter->disableIdFilter(); // 去掉默认的id过滤器
                $filter->like('description', 'Description');
            });*/

            // 关闭全部操作
            /*$moneyBill->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                // 去掉查看
                $actions->disableView();
            });*/
            $moneyBill->disableActions();

            // 去掉批量操作
            /*$moneyBill->batchActions(function ($batch) {
                $batch->disableDelete();
            });*/
            $moneyBill->disableBatchActions();

            $moneyBill->type_text('Type');
            $moneyBill->description('Description');
            $moneyBill->operator_number('流水金额');
        });

        $show->withdraws('提现记录', function ($withdraw) {
            $withdraw->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            // $withdraw->disableCreation(); // Deprecated
            $withdraw->disableCreateButton();

            // 禁用筛选
            $withdraw->disableFilter();

            // 禁用导出数据按钮
            // $withdraw->disableExport();

            /*自定义筛选框*/
            /*$withdraw->filter(function ($filter) {
                $filter->disableIdFilter(); // 去掉默认的id过滤器
                $filter->like('description', 'Description');
            });*/

            // 关闭全部操作
            /*$withdraw->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                // 去掉查看
                $actions->disableView();
            });*/
            $withdraw->disableActions();

            // 去掉批量操作
            /*$withdraw->batchActions(function ($batch) {
                $batch->disableDelete();
            });*/
            $withdraw->disableBatchActions();

            $withdraw->type_text('Type');
            $withdraw->status_text('Status');
            $withdraw->money('金额');
            $withdraw->info('提现预留信息');
            $withdraw->reason('回复拒绝原因等信息');
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        $form->image('avatar', 'Avatar')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        // $form->text('name', 'Name');
        $form->display('name', 'Name')->rules('required|string');
        // $form->text('gender', 'Gender');
        $form->display('gender', 'Gender');
        // $form->mobile('phone', 'Phone');
        $form->display('phone', 'Phone');
        // $form->image('avatar', 'Avatar');
        // $form->decimal('money', '奖励金')->default(0.00);
        $form->display('money', '奖励金')->default(0.00);
        $form->display('frozen_money', '冻结金额')->default(0.00);
        $form->display('total_client_order_money', '累计投递订单金额')->default(0.00);
        $form->display('total_client_order_count', '累计投递订单次数')->default(0.00);
        // $form->text('wx_openid', '微信 openid');
        $form->display('wx_openid', '微信 openid');
        // $form->text('wx_country', '微信 country');
        $form->display('wx_country', '微信 country');
        // $form->text('wx_province', '微信 province');
        $form->display('wx_province', '微信 province');
        // $form->text('wx_city', '微信 city');
        $form->display('wx_city', '微信 city');
        $form->display('real_id', '身份证号');
        $form->display('real_name', '真实姓名');
        $form->switch('is_authenticated', '是否实名验证')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);
        // $form->email('email', 'Email');
        $form->display('email', 'Email');
        // $form->datetime('email_verified_at', 'Email verified at'))->default(date('Y-m-d H:i:s');
        // $form->password('password', 'Password');

        return $form;
    }
}
