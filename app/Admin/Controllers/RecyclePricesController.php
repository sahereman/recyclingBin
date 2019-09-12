<?php

namespace App\Admin\Controllers;

use App\Models\CleanPrice;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RecyclePricesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回收端价格';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CleanPrice);

        $grid->column('id', 'Id')->sortable();
        $grid->column('slug', 'Slug')->sortable();
        $grid->column('price', 'Price')->sortable();

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
        $show = new Show(CleanPrice::findOrFail($id));

        // $show->field('id', 'Id');
        $show->field('slug', 'Slug');
        $show->field('price', 'Price');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CleanPrice);

        $form->text('slug', 'Slug')->rules('required|string');
        $form->decimal('price', 'Price')->setWidth(2)->default(0.01)->rules('required|numeric|min:0.01');

        return $form;
    }
}
