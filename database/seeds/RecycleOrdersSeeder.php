<?php

use Illuminate\Database\Seeder;
use App\Models\RecycleOrder;
use App\Models\RecycleOrderItem;
use App\Admin\Models\Bin;
use App\Models\RecyclerMoneyBill;
use App\Models\Recycler;
use App\Jobs\GenerateRecycleOrderSnapshot;

class RecycleOrdersSeeder extends Seeder
{
    public function run()
    {
        $recycler = Recycler::find(1);
        $bins = Bin::all();

        for ($i = 1; $i <= 20; $i++)
        {
            $order = factory(RecycleOrder::class)->create([
                'recycler_id' => $recycler->id,
            ]);

            factory(RecycleOrderItem::class, random_int(1, 2))->create([
                'order_id' => $order,
            ]);

            GenerateRecycleOrderSnapshot::dispatch($order, $bins->random());
            RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_RECYCLE_ORDER, $order->total, $order);
        }
    }
}
