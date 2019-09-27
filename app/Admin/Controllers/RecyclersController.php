<?php

namespace App\Admin\Controllers;

use App\Models\Bin;
use App\Models\Recycler;
use App\Http\Requests\Request;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Foundation\Validation\ValidatesRequests;

class RecyclersController extends AdminController
{
    use ValidatesRequests;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回收员';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Recycler);
        // $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '昵称');
                $filter->like('phone', '手机号');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('money', '余额');
            });
        });

        $grid->column('id', 'ID')->sortable();
        $grid->column('avatar', '头像')->image('', 40);
        $grid->column('name', '昵称');
        $grid->column('phone', '手机号');
        $grid->column('money', '余额')->sortable();
        // $grid->column('frozen_money', '冻结金额');
        $grid->column('bins', 'Bins')->display(function ($bins) {
            return count($bins);
        });
        // $grid->column('password', 'Password');
        $grid->column('created_at', 'Created at');
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
        $show = new Show(Recycler::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('avatar', '头像')->image('', 120);
        $show->field('name', '昵称');
        $show->field('phone', '手机号');
        $show->field('money', '余额');
        $show->field('frozen_money', '冻结金额');
        // $show->field('password', 'Password');
        $show->field('created_at', 'Created at');
        // $show->field('updated_at', 'Updated at');

        $show->bins('回收垃圾桶', function ($bin) {
            $bin->model()->orderBy('bin_recyclers.created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $bin->disableCreateButton();
            $bin->disableFilter();
            $bin->disableActions();
            $bin->disableBatchActions();

            $bin->column('id', 'Id')->sortable();
            // $bin->column('site_id', 'Site id');
            $bin->site()->name('Site');
            $bin->column('is_run', 'Is Run')->switch([
                'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
            ]);
            $bin->column('name', 'Name')->expand(function ($model) {
                $types[0] = $model->type_fabric->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'clean_price_value');
                $types[1] = $model->type_paper->only('name', 'status_text', 'number', 'unit', 'client_price_value', 'clean_price_value');
                return new Table(['Type', 'Status', 'Number', 'Unit', '客户端价格', '回收端价格'], $types);
            });
            $bin->column('no', 'No.');
            $bin->column('lat', 'Latitude');
            $bin->column('lng', 'Longitude');
            $bin->column('address', 'Address');
        });

        $show->moneyBills('账单记录', function ($moneyBill) {
            $moneyBill->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $moneyBill->disableCreateButton();
            $moneyBill->disableFilter();
            $moneyBill->disableActions();
            $moneyBill->disableBatchActions();

            // grid
            $moneyBill->created_at('时间');
            $moneyBill->type_text('类型');
            $moneyBill->description('描述');
            $moneyBill->operator_number('金额');
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Recycler);

        $form->text('name', '昵称');
        $form->mobile('phone', '手机号');
        $form->image('avatar', '头像')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        $form->decimal('money', '余额')->default(0.00);
        $form->decimal('frozen_money', '冻结金额')->default(0.00);
        // $form->password('password', 'Password');
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }

    // GET: 回收员指派 页面
    public function assignmentShow(Content $content)
    {
        return $content
            ->header('回收员指派')
            ->body($this->assignmentForm());
    }

    protected function assignmentForm()
    {
        $form = new Form(new Bin());

        $form->setAction(route('admin.recyclers.assignment.store'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->select('recycler_id')->options(Recycler::all()->pluck('name', 'id'));

        $form->listbox('bin_ids', '请选择回收箱')->options(Bin::all()->pluck('full_name', 'id'));

        return $form;
    }

    // POST: 回收员指派 请求处理
    public function assignmentStore(Request $request, Content $content)
    {
        $data = $this->validate($request, [
            'bin_ids' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (Bin::whereIn('id', request()->input($attribute))->count() == 0) {
                        $fail('请选择回收箱');
                    }
                },
            ],
            'recycler_id' => 'required|integer'
        ], [], [
            'bin_ids' => '回收箱 IDs',
            'recycler_id' => '回收员'
        ]);

        $recycler_id = $data['recycler_id'];
        $bin_ids = $data['bin_ids'];
        $key = array_search(NULL, $bin_ids, true);
        if ($key !== false) {
            unset($bin_ids[$key]);
        }
        $recycler = Recycler::find($recycler_id);
        $recycler->bins()->sync($bin_ids);

        return $content
            ->row("<center><h3>回收员指派成功！</h3></center>")
            // ->row("<center><a href='/admin/recyclers'>返回 回收员 列表</a></center>");
            ->row("<center><a href='" . route('recyclers.index') . "'>返回 回收员 列表</a></center>");
    }
}
