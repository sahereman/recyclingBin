<?php

namespace App\Admin\Controllers;

use App\Admin\Models\ClientPrice;
use App\Jobs\GenerateBinTypeSnapshot;
use App\Models\Bin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ClientPricesController extends AdminController
{
    protected $title = '客户端价格';

    protected function grid()
    {
        $grid = new Grid(new ClientPrice());
        $grid->disableCreateButton();
        $grid->disablePagination();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });

        $grid->column('slug_text', '分类箱');
        $grid->column('price', '价格');
        $grid->column('unit', '单位');

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new ClientPrice);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->display('slug_text', '分类箱');
        $form->decimal('price', '价格')->setWidth(2)->default(0.01)->rules('required|numeric|min:0.01');
        $form->display('unit', '单位');

        //保存后回调
        $form->saved(function (Form $form) {
            $bins = Bin::all();

            $bins->each(function ($bin) {
                GenerateBinTypeSnapshot::dispatch($bin);
            });
        });

        return $form;
    }
}
