<?php

namespace App\Admin\Controllers;

use App\Models\BinTypePaper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FullPapersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '可回收物-满箱预警';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BinTypePaper);

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

        $grid->model()->where('status', BinTypePaper::STATUS_FULL)->with('bin')->orderBy('number', 'desc'); // 设置初始排序条件

        $grid->column('name', '名称');
        $grid->bin()->no('回收箱编号');
        $grid->bin()->name('回收箱');
        $grid->column('status_text', '状态');
        $grid->column('number', '数量');
        $grid->column('unit', '单位');

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(BinTypePaper::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bin_id', __('Bin id'));
        $show->field('name', __('Name'));
        $show->field('status', __('Status'));
        $show->field('number', __('Number'));
        $show->field('unit', __('Unit'));
        $show->field('threshold', __('Threshold'));
        $show->field('real_weight', __('Real weight'));
        $show->field('client_price_id', __('Client price id'));
        $show->field('clean_price_id', __('Clean price id'));

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BinTypePaper);

        $form->number('bin_id', __('Bin id'));
        $form->text('name', __('Name'));
        $form->text('status', __('Status'));
        $form->decimal('number', __('Number'));
        $form->text('unit', __('Unit'));
        $form->decimal('threshold', __('Threshold'))->default(0.00);
        $form->decimal('real_weight', __('Real weight'))->default(0.00);
        $form->number('client_price_id', __('Client price id'));
        $form->number('clean_price_id', __('Clean price id'));

        return $form;
    }
}
