<?php

use Illuminate\Database\Seeder;
use App\Models\CleanOrder;
use App\Models\CleanOrderItem;
use App\Models\RecyclerMoneyBill;
use App\Models\Recycler;
use App\Jobs\GenerateCleanOrderSnapshot;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Clean\CleanOrderCompletedNotification;

class CleanOrdersSeeder extends Seeder
{
    public function run()
    {
        $recyclers = Recycler::all();

        $recyclers->each(function ($recycler) {
            $bins = $recycler->bins;

            if ($bins->isNotEmpty())
            {
                for ($i = 1; $i <= 20; $i++)
                {
                    $bin = $bins->random();
                    $order = factory(CleanOrder::class)->create([
                        'bin_id' => $bin->id,
                        'recycler_id' => $recycler->id,
                    ]);

                    factory(CleanOrderItem::class, random_int(1, 2))->create([
                        'order_id' => $order->id,
                    ]);

                    GenerateCleanOrderSnapshot::dispatch($order, $bin);
                    RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_CLEAN_ORDER, $order->total, $order);
                    Notification::send($recycler, new CleanOrderCompletedNotification($order));
                }
            }

        });

    }
}
