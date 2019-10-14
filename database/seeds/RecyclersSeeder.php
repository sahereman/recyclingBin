<?php

use Illuminate\Database\Seeder;
use App\Models\Recycler;
use App\Models\RecyclerDeposit;
use App\Models\RecyclerMoneyBill;
use App\Models\RecyclerWithdraw;
use App\Notifications\Clean\RecyclerWithdrawAgreeNotification;
use App\Notifications\Clean\RecyclerWithdrawDenyNotification;

class RecyclersSeeder extends Seeder
{

    public function run()
    {
        // 通过 factory 方法生成 x 个数据并保存到数据库中
        factory(Recycler::class, 5)->create();

        // 单独处理第一个用户的数据
        $recycler = Recycler::find(1);
        $recycler->phone = '18600982820';
        $recycler->save();

        // 提现数据
        for ($i = 1; $i <= 10; $i++)
        {
            $withdraw = factory(RecyclerWithdraw::class)->create([
                'recycler_id' => $recycler,
            ]);

            switch ($withdraw->type)
            {
                case RecyclerWithdraw::TYPE_UNION_PAY :
                    $withdraw->update([
                        'info' => [
                            'name' => '李四',
                            'bank' => '中国农业银行',
                            'account' => '62223078323174632',
                            'bank_name' => 'XXX支行',
                        ]
                    ]);
                    break;
            }

            switch ($withdraw->status)
            {
                case RecyclerWithdraw::STATUS_WAIT :
                    $recycler->update([
                        'frozen_money' => bcadd($recycler->frozen_money, $withdraw->money, 2)
                    ]);
                    break;
                case RecyclerWithdraw::STATUS_AGREE :
                    RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_RECYCLER_WITHDRAW, $withdraw->money, $withdraw);
                    $withdraw->recycler->notify(new RecyclerWithdrawAgreeNotification($withdraw));
                    $withdraw->checked_at = now();
                    $withdraw->save();
                    break;
                case RecyclerWithdraw::STATUS_DENY :
                    $withdraw->update([
                        'reason' => '回收员银行预留信息错误',
                        'checked_at'=>now(),
                    ]);
                    $withdraw->recycler->notify(new RecyclerWithdrawDenyNotification($withdraw));
                    break;
            }

        }
    }
}
