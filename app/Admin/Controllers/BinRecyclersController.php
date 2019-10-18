<?php

namespace App\Admin\Controllers;

use App\Models\BinRecycler;
use App\Models\Recycler;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BinRecyclersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回收箱权限';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BinRecycler);
        $grid->disableCreateButton();
        $grid->disablePagination();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<div class="btn-group pull-right" style="margin-right: 10px">
                                <a href="' . route('admin.recyclers.index') . '" class="btn btn-sm btn-default" >
                                <i class="fa fa-backward"></i>&nbsp;返回</a>
                            </div>');
        });

        $recycler = Recycler::find(request()->input('recycler_id'));
        if ($recycler instanceof Recycler)
        {
            $grid->model()->where('recycler_id', $recycler->id);
        }

        $grid->bin()->name('设备名称');
        $grid->fabric_permission('纺织物开箱权限')->switch();
        $grid->paper_permission('可回收物开箱权限')->switch();

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
        $show = new Show(BinRecycler::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bin_id', __('Bin id'));
        $show->field('recycler_id', __('Recycler id'));
        $show->field('fabric_permission', __('Fabric permission'));
        $show->field('paper_permission', __('Paper permission'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BinRecycler);

        $form->number('bin_id', __('Bin id'));
        $form->number('recycler_id', __('Recycler id'));
        $form->switch('fabric_permission', __('Fabric permission'))->default(1);
        $form->switch('paper_permission', __('Paper permission'))->default(1);

        return $form;
    }
}
