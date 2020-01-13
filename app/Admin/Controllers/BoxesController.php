<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExporters\BoxsExcelExporter;
use App\Admin\Extensions\ExcelExporters\ExcelExporter;
use App\Models\Box;
use App\Models\BoxOrder;
use App\Models\ServiceSite;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class BoxesController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '传统箱';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Box);

        $grid->model()->with([
            'site',
        ])->orderBy('id', 'asc'); // 设置初始排序条件

        $admin_user = Auth::guard('admin')->user();

        if ($admin_user->isRole('box_admin'))
        {
            $grid->model()->whereIn('id', $admin_user->boxes->pluck('id')->all());
        }


        $grid->exporter(new BoxsExcelExporter());

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '箱体名称');
                $filter->equal('site_id', '箱体站点')->select(ServiceSite::all()->pluck('name', 'id')->toArray());
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('no', '箱体编号');
                $filter->like('address', '地址');
            });
        });
        $grid->id("ID")->hide();
        $grid->column('no', '箱体编号')->qrcode(function ($no) {
            return url('client/qr') . '?box_no=' . $no;
        })->sortable();
        $grid->site()->name('站点名称');
        $grid->column('name', '箱体名称');
        $grid->column('address', '地址');

        $grid->column('orders', '待审核订单')->display(function ($orders) {
            $filtered = array_where($orders, function ($value) {
                return $value['status'] == BoxOrder::STATUS_WAIT;
            });
            return count($filtered);
        });

        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.box_orders.index', ['box_id' => $this->id]) . '">传统箱订单</a>';
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
        $show = new Show(Box::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('site_name', '站点名称');
        $show->field('name', '箱体名称');
        $show->field('no', '箱体编号');
        $show->field('address', '地址');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');
        $show->field('', '经纬度地图')->latlong('lat', 'lng', $height = 400);

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Box);
        $sites = ServiceSite::all()->pluck('name', 'id')->toArray();

        $form->select('site_id', '选择站点')->options($sites)->rules('required')->required();
        $form->text('name', '箱体名称')->rules('required|string');
        $form->hidden('status', '满箱状态')->value(Box::STATUS_NORMAL);
        //        $form->text('no', '箱体编号')->rules('required|string');
        $form->text('address', '地址')->rules('required|string');
        $form->latlong('lat', 'lng', '经纬度选择器')->default(['lat' => 36.093187, 'lng' => 120.381310])->required();

        return $form;
    }
}
