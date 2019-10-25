<?php

namespace App\Admin\Controllers;

use App\Models\Bin;
use App\Models\ClientOrder;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class ClientOrdersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '投递订单';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ClientOrder);
        $grid->disableExport();
        $grid->model()->with(['items','bin'])->orderBy('created_at', 'desc'); // 设置初始排序条件
        $user = User::find(request()->input('user_id'));
        $bin = Bin::find(request()->input('bin_id'));
        if ($user instanceof User)
        {
            $grid->model()->where('user_id', $user->id);
        }
        if ($bin instanceof Bin)
        {
            $grid->model()->where('bin_id', $bin->id);
        }

        /*禁用*/
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

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
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('sn', '订单号');
                $filter->between('created_at', '投递时间')->datetime();
            });
        });

        $grid->created_at('投递时间')->sortable();
        $grid->sn('订单号')->expand(function ($model) {
            $item = $model->items->map(function ($item) {
                return $item->only(['type_name', 'number', 'unit','subtotal']);
            });
            return new Table(['分类箱', '数量', '单位','小计'], $item->toArray());
        });;
        $grid->user('用户')->display(function ($user) {
            return "<a href='" . route('admin.users.show', $user['id']) . "'>$user[name]</a>";
        });
        $grid->bin('回收箱')->display(function ($bin) {
            return $bin ? "<a href='" . route('admin.bins.show', $bin['id']) . "'>$bin[name]</a>" : '';
        });
        $grid->status_text('状态');
        $grid->total('合计')->sortable();

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ClientOrder::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
        });

        $show->id('ID');
        $show->sn('订单号');
        $show->status_text('状态');
        $show->total('合计');
        $show->created_at('投递时间');

        $show->items('投递详情', function ($item) {
            /*禁用*/
            $item->disableCreateButton();
            $item->disableFilter();
            $item->disableExport();
            $item->disableActions();
            $item->disableBatchActions();
            $item->disablePagination();

            $item->type_name('分类箱');
            $item->number('数量');
            $item->unit('单位');
            $item->subtotal('小计');
        });

        $show->user('投递用户信息', function ($user) {
            /*禁用*/
            $user->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });

            $user->field('id', 'ID');
            $user->field('avatar', '头像')->image('', 120);
            $user->field('name', '昵称');
            $user->field('gender', '性别');
            $user->field('phone', '手机号');
            $user->field('money', '奖励金');
            $user->field('frozen_money', '冻结金额');
            $user->field('total_client_order_money', '累计投递订单金额');
            $user->field('total_client_order_count', '累计投递订单次数');
        });





        return $show;
    }

    protected function form()
    {
        $form = new Form(new ClientOrder);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        return $form;
    }
}
