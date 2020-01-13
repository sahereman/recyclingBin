<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Ajax\Ajax_Button;
use App\Admin\Extensions\Ajax\Ajax_Input_Text_Button;
use App\Admin\Extensions\ExcelExporters\BoxOrdersExcelExporter;
use App\Admin\Extensions\ExcelExporters\ExcelExporter;
use App\Http\Requests\Request;
use App\Models\Box;
use App\Models\BoxOrder;
use App\Models\Config;
use App\Models\User;
use App\Models\UserMoneyBill;
use App\Notifications\Client\BoxOrderCompletedNotification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BoxOrdersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '传统箱订单';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BoxOrder);
        $grid->model()->with('box')->orderBy('status', 'desc')->orderBy('created_at', 'desc'); // 设置初始排序条件

        $admin_user = Auth::guard('admin')->user();

        if ($admin_user->isRole('box_admin'))
        {
            $grid->model()->whereIn('box_id', $admin_user->boxes->pluck('id')->all());
        }

        $user = User::find(request()->input('user_id'));
        $box = Box::find(request()->input('box_id'));
        if ($user instanceof User)
        {
            $grid->model()->where('user_id', $user->id);
        }
        if ($box instanceof Box)
        {
            $grid->model()->where('box_id', $box->id);
        }

        $grid->exporter(new BoxOrdersExcelExporter());

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
                    $query->whereHas('user', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });
                }, '用户');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('phone', 'like', "%{$this->input}%");
                    });
                }, '手机号');
                $filter->equal('status', '状态')->select(BoxOrder::$StatusMap);

            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('sn', '订单号');
                $filter->between('created_at', '投递时间')->datetime();
            });
        });
        $grid->id("ID")->hide();
        $grid->created_at('投递时间')->sortable();
        $grid->sn('订单号')->sortable();

        $grid->user('用户')->display(function ($user) {
            return "<a href='" . route('admin.users.show', $user['id']) . "'>$user[name]</a>";
        });
        $grid->box('传统箱')->display(function ($box) {
            return $box ? "<a href='" . route('admin.boxes.show', $box['id']) . "'>$box[name]</a>" : '';
        });
        $grid->status_text('状态');
        $grid->total('奖励金')->display(function ($total) {
            if ($this->status == BoxOrder::STATUS_COMPLETED)
            {
                return $total == 0 ? '奖励次数限制' : $total;
            } else
            {
                return '';
            }
        });

        $grid->column('image_proof', '图片凭证')->image('', 60, 60);

        $grid->column('manage', '管理')->display(function () use ($admin_user) {
            $buttons = '';
            $buttons .= '<a target="_blank" class="btn btn-xs btn-primary" style="margin-right:6px" href="' . $this->image_proof_url . '">查看图片凭证</a>';
            if ($this->status == BoxOrder::STATUS_WAIT)
            {
                if ($admin_user->can('box_orders.check'))
                {
                    $buttons .= new Ajax_Input_Text_Button(route('admin.box_orders.agree', $this->id), [], '审核通过', '请输入奖励金额', Config::config('box_order_profit_money'));
                    $buttons .= new Ajax_Button(route('admin.box_orders.deny', $this->id), [], '奖励限制');
                }
            }
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
        $show = new Show(BoxOrder::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
        });

        $show->id('ID');
        $show->sn('订单号');
        $show->total('奖励金')->as(function ($total) {
            return $total == 0 ? '奖励次数限制' : $total;
        });
        $show->created_at('投递时间');
        $show->image_proof('图片凭证')->image();

        $show->user('投递用户信息', function ($user) {
            /*禁用*/
            $user->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });

            $user->field('id', 'ID');
            $user->field('avatar', '头像')->image('', 120);
            $user->field('name', '昵称');
            $user->field('gender', '性别');
            $user->field('phone', '手机号');
            $user->field('money', '奖励金');
            $user->field('frozen_money', '冻结金额');
            $user->field('total_client_order_money', '累计投递订单金额');
            $user->field('total_client_order_count', '累计投递订单次数');
        });


        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BoxOrder);


        return $form;
    }


    public function agree(Request $request, BoxOrder $order)
    {

        if ($order->status !== BoxOrder::STATUS_WAIT)
        {
            return response()->json([
                'status' => false,
                'message' => '状态异常'
            ]);
        }

        // 验证
        $data = Validator::make($request->all(), [
            'input' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [], [
            'input' => '奖励金额',
        ])->validate();

        // 审核成功,修改用户金额,修改订单状态,通知用户,改变账单
        $user = $order->user;
        $order->update([
            'status' => BoxOrder::STATUS_COMPLETED,
            'total' => $data['input'],
        ]);
        $user->update([
            'money' => bcadd($user->money, $data['input'], 2),
        ]);
        UserMoneyBill::change($user, UserMoneyBill::TYPE_BOX_ORDER, $data['input'], $order);
        $user->notify(new BoxOrderCompletedNotification($order));


        return response()->json([
            'status' => true,
            'message' => '审核成功'
        ]);
    }

    public function deny(Request $request, BoxOrder $order)
    {
        if ($order->status !== BoxOrder::STATUS_WAIT)
        {
            return response()->json([
                'status' => false,
                'message' => '状态异常'
            ]);
        }

        // 奖励限制,修改订单状态
        $order->update([
            'status' => BoxOrder::STATUS_COMPLETED,
            'total' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => '奖励限制'
        ]);

    }
}
