<?php

namespace App\Admin\Controllers;

use App\Models\ClientOrder;
use App\Models\ClientOrderItem;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ClientOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ClientOrder);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        // 关闭全部操作
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            // $actions->disableEdit();
            // 去掉查看
            // $actions->disableView();
        });
        // 关闭全部操作
        // $grid->disableActions();

        $grid->column('id', 'Id')->sortable();
        // $grid->column('user_id', 'User id')->sortable();
        $grid->user()->name('User')->sortable();
        // $grid->column('status', 'Status')->sortable();
        $grid->column('status_text', 'Status');
        // $grid->column('bin_snapshot', 'Bin snapshot');
        $grid->column('total', 'Total')->sortable();
        // $grid->column('created_at', 'Created at');
        // $grid->column('updated_at', 'Updated at');

        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建订单
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
        $show = new Show(ClientOrder::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });

        // $show->field('id', 'Id');
        // $show->field('user_id', 'User id');

        $show->user('用户信息', function ($user) {
            /*禁用*/
            $user->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });
            $user->name('Name');
            $user->gender('Gender');
            $user->phone('Phone');
        });

        $show->items('订单详情', function ($item) {
            /*禁用*/
            // $item->disableCreation(); // Deprecated
            $item->disableCreateButton();

            // 禁用筛选
            $item->disableFilter();

            // 禁用导出数据按钮
            // $item->disableExport();

            // 关闭全部操作
            /*$item->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                // 去掉查看
                $actions->disableView();
            });*/
            $item->disableActions();

            // 去掉批量操作
            /*$item->batchActions(function ($batch) {
                $batch->disableDelete();
            });*/
            $item->disableBatchActions();

            $item->type_name('Type');
            $item->number('Number');
            $item->unit('Unit');
            $item->subtotal('小计');
        });

        // $show->field('status', 'Status');
        $show->field('status_text', 'Status');
        // $show->field('bin_snapshot', 'Bin snapshot');
        $show->field('total', 'Total');
        $show->field('created_at', 'Created at');
        $show->field('updated_at', 'Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ClientOrder);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        // $form->number('user_id', 'User id');
        $users = User::all()->pluck('name', 'id');
        $form->select('user_id', 'User')->options($users)->readOnly();
        // $form->text('status', 'Status')->default('completed');
        $form->display('status_text', 'Status')->default('completed');
        // $form->text('bin_snapshot', 'Bin snapshot');
        // $form->decimal('total', 'Total');
        $form->display('total', 'Total')->setWidth(2)->default(0.01)->rules('required|numeric|min:0.01');

        $form->hasMany('items', '订单详情', function ($item) {
            $item->display('type_name', 'Type')->setWidth(3);
            $item->display('number', 'Number')->setWidth(2);
            $item->display('unit', 'Unit')->setWidth(2);
            $item->display('subtotal', '小计')->setWidth(2)->default(0.01);
        })->disableCreate()->disableDelete()->readonly();

        return $form;
    }
}
