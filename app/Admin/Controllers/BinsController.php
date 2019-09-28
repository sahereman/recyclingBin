<?php

namespace App\Admin\Controllers;

use App\Jobs\GenerateBinTypeSnapshot;
use App\Models\Bin;
use App\Models\BinTypeFabric;
use App\Models\BinTypePaper;
use App\Models\ClientPrice;
use App\Models\CleanPrice;
use App\Models\Recycler;
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
     * @var string
     */
    protected $title = '回收箱';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bin);
        $grid->model()->with([
            'site',
            'type_paper', 'type_paper.client_price', 'type_paper.clean_price',
            'type_fabric', 'type_fabric.client_price', 'type_fabric.clean_price',
        ])->orderBy('id', 'asc'); // 设置初始排序条件
        $recycler = Recycler::find(request()->input('recycler_id'));
        if ($recycler instanceof Recycler)
        {
            //            $grid->model()->recyclers()->where('recycler_id', $recycler->id);
        }

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '设备名称');
                $filter->equal('site_id','设备站点')->select(ServiceSite::all()->pluck('name', 'id')->toArray());
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('no', '设备编号');
                $filter->like('address', '地址');
                $filter->where(function ($query) {
                    switch ($this->input)
                    {
                        case 'yes':
                            $query->where('is_run', true);
                            break;
                        case 'no':
                            $query->where('is_run', false);
                            break;
                    }
                }, '正在运行')->radio([
                    'all' => '不选择',
                    'yes' => '是',
                    'no' => '否',
                ]);
            });
        });


        $grid->column('id', 'ID')->sortable();
        $grid->site()->name('站点名称');
        $grid->column('no', '设备编号');
        $grid->column('name', '设备名称')->expand(function ($model) {
            $types[0] = $model->type_fabric->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'clean_price_value');
            $types[1] = $model->type_paper->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'clean_price_value');
            return new Table(['类型箱', '状态', '数量', '单位', '客户端价格', '回收端价格'], $types);
        });
        $grid->column('address', '地址');

        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.client_orders.index', ['bin_id' => $this->id]) . '">投递订单</a>';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.clean_orders.index', ['bin_id' => $this->id]) . '">回收订单</a>';
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
        $show = new Show(Bin::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('site_name', '站点名称');
        $show->field('is_run', '正在运行')->as(function ($is_run) {
            return $is_run ? '是' : '否';
        });
        $show->field('name', '设备名称');
        $show->field('no', '设备编号');
        $show->field('address', '地址');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');
        $show->field('','经纬度地图')->latlong('lat', 'lng', $height = 400);
        // $show->field('types_snapshot', 'Types snapshot')->json();
        $show->type_fabric(BinTypeFabric::NAME, function ($type_fabric) {
            /*禁用*/
            $type_fabric->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });
            $type_fabric->name('分类箱');
            $type_fabric->status_text('状态');
            $type_fabric->number('数量');
            $type_fabric->unit('单位');
            $type_fabric->client_price_value('客户端价格');
            $type_fabric->clean_price_value('回收端价格');
        });
        $show->type_paper(BinTypePaper::NAME, function ($type_paper) {
            /*禁用*/
            $type_paper->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });
            $type_paper->name('分类箱');
            $type_paper->status_text('状态');
            $type_paper->number('数量');
            $type_paper->unit('单位');
            $type_paper->client_price_value('客户端价格');
            $type_paper->clean_price_value('回收端价格');
        });


        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Bin);
        $sites = ServiceSite::all()->pluck('name', 'id')->toArray();


        $form->select('site_id', '选择站点')->options($sites)->rules('required')->required();
        $form->text('name', '设备名称')->rules('required|string');
        $form->text('no', '设备编号')->rules('required|string');
        $form->text('address', '地址')->rules('required|string');
        $form->latlong('lat', 'lng', '经纬度选择器')->default(['lat' => 36.093187, 'lng' => 120.381310])->required();
        $form->switch('is_run', '正在运行')->states([
            'on' => ['value' => 1, 'text' => '开', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '关', 'color' => 'default'],
        ])->default(1);

        $form->divider();

        $form->display('type_fabric.name', '分类箱')->default(BinTypeFabric::NAME);
        $form->hidden('type_fabric.name', '分类箱')->default(BinTypeFabric::NAME)->rules('required|string');
        $form->select('type_fabric.status', '状态')->options(BinTypeFabric::$StatusMap)->default(BinTypeFabric::STATUS_NORMAL)->required();
        $form->decimal('type_fabric.number', '数量')->setWidth(2)->default(0.00)->rules('required|numeric|min:0');
        $form->display('type_fabric.unit', '单位')->setWidth(2)->default('公斤');
        $form->hidden('type_fabric.unit', '单位')->setWidth(2)->default('公斤')->rules('required|string');
        $fabric_client_prices = ClientPrice::where('slug', 'fabric')->get()->pluck('price', 'id')->toArray();
        $form->select('type_fabric.client_price_id', '客户端价格')->options($fabric_client_prices)->default(array_keys($fabric_client_prices)[0])->required();
        $fabric_clean_prices = CleanPrice::where('slug', 'fabric')->get()->pluck('price', 'id')->toArray();
        $form->select('type_fabric.clean_price_id', '回收端价格')->options($fabric_clean_prices)->default(array_keys($fabric_clean_prices)[0])->required();

        $form->divider();
        $form->display('type_paper.name', '分类箱')->default(BinTypePaper::NAME);
        $form->hidden('type_paper.name', '分类箱')->default(BinTypePaper::NAME)->rules('required|string');
        $form->select('type_paper.status', '状态')->options(BinTypePaper::$StatusMap)->default(BinTypePaper::STATUS_NORMAL)->required();
        $form->decimal('type_paper.number', '数量')->setWidth(2)->default(0.00)->rules('required|numeric|min:0');
        $form->display('type_paper.unit', '单位')->setWidth(2)->default('公斤');
        $form->hidden('type_paper.unit', '单位')->setWidth(2)->default('公斤')->rules('required|string');

        $paper_client_prices = ClientPrice::where('slug', 'paper')->get()->pluck('price', 'id')->toArray();
        $form->select('type_paper.client_price_id', '客户端价格')->options($paper_client_prices)->default(array_keys($paper_client_prices)[0])->required();

        $paper_clean_prices = CleanPrice::where('slug', 'paper')->get()->pluck('price', 'id')->toArray();
        $form->select('type_paper.clean_price_id', '回收端价格')->options($paper_clean_prices)->default(array_keys($paper_clean_prices)[0])->required();

        $form->saving(function (Form $form) {
            if(!$form->model()->types_snapshot)
            {
                $form->model()->types_snapshot = [];
            }
        });

        //保存后回调
        $form->saved(function (Form $form) {
            GenerateBinTypeSnapshot::dispatch($form->model());
        });

        return $form;
    }
}
