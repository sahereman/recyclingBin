<?php

namespace App\Admin\Controllers;

use App\Models\Bin;
use App\Models\BinTypeFabric;
use App\Models\BinTypePaper;
use App\Models\ClientPrice;
use App\Models\RecyclePrice;
use App\Models\ServiceSite;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class BinsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回收垃圾桶';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bin);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        $grid->column('id', 'Id')->sortable();
        // $grid->column('site_id', 'Site id');
        $grid->site()->name('Site');
        $grid->column('is_run', 'Is Run')->switch([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);
        $grid->column('name', 'Name')->expand(function ($model) {
            $types[0] = $model->type_fabric->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'recycle_price_value');
            $types[1] = $model->type_paper->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'recycle_price_value');
            return new Table(['Type', 'Status', 'Number', 'Unit', '客户端价格', '回收端价格'], $types);
        });
        $grid->column('no', 'No.');
        $grid->column('lat', 'Latitude');
        $grid->column('lng', 'Longitude');
        $grid->column('address', 'Address');
        // $grid->column('types_snapshot', 'Types snapshot');
        // $grid->column('created_at', 'Created at');
        // $grid->column('updated_at', 'Updated at');

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
        $show = new Show(Bin::findOrFail($id));

        $show->field('id', 'Id');
        // $show->field('site_id', 'Site id');
        $show->field('site_name', 'Site');
        $show->field('is_run', 'Is Run')->as(function ($is_run) {
            return $is_run ? 'Yes' : 'No';
        });
        $show->field('name', 'Name');
        $show->field('no', 'No.');
        $show->field('lat', 'Latitude');
        $show->field('lng', 'Longitude');
        $show->field('address', 'Address');
        // $show->field('types_snapshot', 'Types snapshot')->json();
        $show->type_fabric('纺织物', function ($type_fabric) {
            /*禁用*/
            $type_fabric->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });
            $type_fabric->name('Type');
            $type_fabric->status_text('Status');
            $type_fabric->number('Number');
            $type_fabric->unit('Unit');
            $type_fabric->client_price_value('客户端价格');
            $type_fabric->recycle_price_value('回收端价格');
        });
        $show->type_paper('纸类、塑料、金属', function ($type_paper) {
            /*禁用*/
            $type_paper->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });
            $type_paper->name('Type');
            $type_paper->status_text('Status');
            $type_paper->number('Number');
            $type_paper->unit('Unit');
            $type_paper->client_price_value('客户端价格');
            $type_paper->recycle_price_value('回收端价格');
        });
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
        $form = new Form(new Bin);

        // $form->number('site_id', 'Site id');
        $sites = ServiceSite::all()->pluck('name', 'id')->toArray();
        $form->select('site_id', 'Site')->options($sites);
        // $form->switch('is_run', 'Is Run')->default(1);
        $form->switch('is_run', 'Is Run')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ])->default(1);
        $form->text('name', 'Name')->rules('required|string');
        $form->text('no', 'No.')->rules('required|string');
        $form->text('lat', 'Latitude')->rules('required|string');
        $form->text('lng', 'Longitude')->rules('required|string');
        $form->text('address', 'Address')->rules('required|string');
        // $form->text('types_snapshot', 'Types snapshot');
        $form->hidden('types_snapshot', 'Types snapshot')->default('[]');

        $form->divider();
        $form->display('type_fabric.name', 'Type')->default('纺织物');
        $form->hidden('type_fabric.name', 'Type')->default('纺织物')->rules('required|string');
        $form->select('type_fabric.status', 'Status')->options(BinTypeFabric::$StatusMap)->default(BinTypeFabric::STATUS_NORMAL);
        $form->decimal('type_fabric.number', 'Number')->setWidth(2)->default(0.00)->rules('required|numeric|min:0');
        $form->display('type_fabric.unit', 'Unit')->setWidth(2)->default('公斤');
        $form->hidden('type_fabric.unit', 'Unit')->setWidth(2)->default('公斤')->rules('required|string');
        $fabric_client_prices = ClientPrice::where('slug', 'fabric')->get()->pluck('price', 'id')->toArray();
        $form->select('type_fabric.client_price_id', '客户端价格')->options($fabric_client_prices)->default(array_keys($fabric_client_prices)[0]);
        $fabric_recycle_prices = RecyclePrice::where('slug', 'fabric')->get()->pluck('price', 'id')->toArray();
        $form->select('type_fabric.recycle_price_id', '回收端价格')->options($fabric_recycle_prices)->default(array_keys($fabric_recycle_prices)[0]);

        $form->divider();
        $form->display('type_paper.name', 'Type')->default('纸类、塑料、金属');
        $form->hidden('type_paper.name', 'Type')->default('纸类、塑料、金属')->rules('required|string');
        $form->select('type_paper.status', 'Status')->options(BinTypePaper::$StatusMap)->default(BinTypePaper::STATUS_NORMAL);
        $form->decimal('type_paper.number', 'Number')->setWidth(2)->default(0.00)->rules('required|numeric|min:0');
        $form->display('type_paper.unit', 'Unit')->setWidth(2)->default('公斤');
        $form->hidden('type_paper.unit', 'Unit')->setWidth(2)->default('公斤')->rules('required|string');
        $paper_client_prices = ClientPrice::where('slug', 'paper')->get()->pluck('price', 'id')->toArray();
        $form->select('type_paper.client_price_id', '客户端价格')->options($paper_client_prices)->default(array_keys($paper_client_prices)[0]);
        $paper_recycle_prices = RecyclePrice::where('slug', 'paper')->get()->pluck('price', 'id')->toArray();
        $form->select('type_paper.recycle_price_id', '回收端价格')->options($paper_recycle_prices)->default(array_keys($paper_recycle_prices)[0]);

        return $form;
    }
}
