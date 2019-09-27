<?php

use Illuminate\Database\Seeder;
use App\Models\CleanOrder;
use App\Models\CleanOrderItem;
use App\Admin\Models\Bin;
use App\Models\RecyclerMoneyBill;
use App\Models\Recycler;
use App\Jobs\GenerateRecycleOrderSnapshot;

class CleanOrdersSeeder extends Seeder
{
    public function run()
    {
        $recycler = Recycler::find(1);
        $bins = $recycler->bins;

        for ($i = 1; $i <= 20; $i++) {
            $bin = $bins->random();
            $order = factory(CleanOrder::class)->create([
                'bin_id' => $bin->id,
                'recycler_id' => $recycler->id,
            ]);

            factory(CleanOrderItem::class, random_int(1, 2))->create([
                'order_id' => $order->id,
            ]);

            GenerateRecycleOrderSnapshot::dispatch($order, $bin);
            RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_RECYCLE_ORDER, $order->total, $order);
        }
    }
}
