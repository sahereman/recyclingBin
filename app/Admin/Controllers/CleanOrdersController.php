<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExporters\CleanOrdersExcelExporter;
use App\Admin\Extensions\ExcelExporters\ExcelExporter;
use App\Models\Bin;
use App\Models\CleanOrder;
use App\Models\Recycler;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class CleanOrdersController extends AdminController
{
    protected $title = '回收订单';

    protected function grid()
    {
        $grid = new Grid(new CleanOrder);
        $grid->model()->with(['items', 'bin'])->orderBy('created_at', 'desc'); // 设置初始排序条件

        $recycler = Recycler::find(request()->input('recycler_id'));
        $bin = Bin::find(request()->input('bin_id'));
        if ($recycler instanceof Recycler)
        {
            $grid->model()->where('recycler_id', $recycler->id);
        }
        if ($bin instanceof Bin)
        {
            $grid->model()->where('bin_id', $bin->id);
        }

        $grid->exporter(new CleanOrdersExcelExporter());

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
                    $query->whereHas('recycler', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });
                }, '回收员');
                $filter->where(function ($query) {
                    $query->whereHas('recycler', function ($query) {
                        $query->where('phone', 'like', "%{$this->input}%");
                    });
                }, '手机号');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('sn', '订单号');
                $filter->between('created_at', '回收时间')->datetime();
            });
        });

        $grid->created_at('回收时间')->sortable();
        $grid->sn('订单号')->expand(function ($model) {
            $item = $model->items->map(function ($item) {
                return $item->only(['type_name', 'number', 'unit', 'subtotal']);
            });
            return new Table(['分类箱', '数量', '单位', '小计'], $item->toArray());
        });;
        $grid->recycler('回收员')->display(function ($recycler) {
            return "<a href='" . route('admin.recyclers.show', $recycler['id']) . "'>$recycler[name]</a>";
        });
        $grid->bin('回收箱')->display(function ($bin) {
            return $bin ? "<a href='" . route('admin.bins.show', $bin['id']) . "'>$bin[name]</a>" : '';
        });
        $grid->status_text('状态');
        $grid->total('合计')->sortable();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(CleanOrder::findOrFail($id));

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

        $show->recycler('回收员信息', function ($user) {
            /*禁用*/
            $user->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });

            $user->field('id', 'ID');
            $user->field('avatar', '头像')->image('', 120);
            $user->field('name', '昵称');
            $user->field('phone', '手机号');
            $user->field('money', '余额');
        });


        return $show;
    }

    protected function form()
    {
        $form = new Form(new CleanOrder);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        return $form;
    }
}
