<?php

namespace App\Admin\Controllers;

use App\Models\Box;
use App\Models\BoxOrder;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BoxOrdersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '传统箱订单';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BoxOrder);
        $grid->disableExport();
        $grid->model()->with('box')->orderBy('created_at', 'desc'); // 设置初始排序条件

        $user = User::find(request()->input('user_id'));
        $box = Box::find(request()->input('box_id'));
        if ($user instanceof User)
        {
            $grid->model()->where('user_id', $user->id);
        }
        if ($box instanceof Box)
        {
            $grid->model()->where('box_id', $box->id);
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
        $grid->sn('订单号')->sortable();

        $grid->user('用户')->display(function ($user) {
            return "<a href='" . route('admin.users.show', $user['id']) . "'>$user[name]</a>";
        });
        $grid->box('传统箱')->display(function ($box) {
            return $box ? "<a href='" . route('admin.boxes.show', $box['id']) . "'>$box[name]</a>" : '';
        });
        $grid->total('奖励金')->display(function ($total) {
            return $total == 0 ? '奖励次数限制' : $total;
        });

        $grid->column('image_proof','图片凭证')->image('',60,60);

        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a target="__blank" class="btn btn-xs btn-primary" style="margin-right:6px" href="' . $this->image_proof_url . '">查看图片凭证</a>';
            return $buttons;
        });

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(BoxOrder::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
        });

        $show->id('ID');
        $show->sn('订单号');
        $show->total('奖励金')->as(function ($total) {
            return $total == 0 ? '奖励次数限制' : $total;
        });
        $show->created_at('投递时间');
        $show->image_proof('图片凭证')->image();

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

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BoxOrder);


        return $form;
    }
}
