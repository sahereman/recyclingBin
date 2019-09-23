<?php

use Illuminate\Database\Seeder;
use App\Models\Recycler;
use App\Models\RecyclerDeposit;
use App\Models\RecyclerMoneyBill;
use App\Models\RecyclerWithdraw;

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

        // 充值数据
        for ($i = 1; $i <= 10; $i++)
        {
            $deposit = factory(RecyclerDeposit::class)->create([
                'recycler_id' => $recycler,
            ]);

            switch ($deposit->status)
            {
                case RecyclerDeposit::STATUS_COMPLETED :
                    $deposit->update([
                        'payment_sn' => str_random(16),
                        'paid_at' => now(),
                    ]);
                    RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_RECYCLER_DEPOSIT, $deposit->money, $deposit);
                    break;
            }

        }

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
                    break;
                case RecyclerWithdraw::STATUS_DENY :
                    $withdraw->update([
                        'reason' => '银行预留信息错误'
                    ]);
                    break;
            }

        }
    }
}