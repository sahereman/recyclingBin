<?php

namespace App\Admin\Controllers;

use App\Models\BinWeightWarning;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BinWeightWarningsController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '重量异常警告';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BinWeightWarning);

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->where(function ($query) {
                $query->whereHas('bin', function ($query) {
                    $query->where('no', 'like', "%{$this->input}%");
                });
            }, '回收箱编号');
        });

        /*禁用*/
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        $grid->model()->with('bin')->orderBy('created_at', 'desc'); // 设置初始排序条件


        $grid->column('id', 'ID');
        $grid->column('created_at', '发生时间')->sortable();
        $grid->bin()->no('回收箱编号');
        $grid->bin()->name('回收箱');
        $grid->column('type_name', '分类名称');
        $grid->column('normal_weight', '正确重量');
        $grid->column('measure_weight', '测量重量');
        $grid->column('exception_weight', '异常减少重量');
        $grid->column('unit', '计量单位');

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(BinWeightWarning::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bin_id', __('Bin id'));
        $show->field('normal_weight', __('Normal weight'));
        $show->field('measure_weight', __('Measure weight'));
        $show->field('exception_weight', __('Exception weight'));
        $show->field('unit', __('Unit'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BinWeightWarning);

        $form->number('bin_id', __('Bin id'));
        $form->decimal('normal_weight', __('Normal weight'));
        $form->decimal('measure_weight', __('Measure weight'));
        $form->decimal('exception_weight', __('Exception weight'));
        $form->text('unit', __('Unit'));

        return $form;
    }
}
