<?php

namespace App\Jobs;

use App\Models\Bin;
use App\Models\ClientOrder;
use App\Models\RecycleOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;


class GenerateRecycleOrderSnapshot
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $bin;

    public function __construct(RecycleOrder $order, Bin $bin)
    {
        $this->order = $order;
        $this->bin = $bin;
    }

    public function handle()
    {
        $order = $this->order;
        $bin = $this->bin;

        $order->bin_snapshot = DB::transaction(function () use ($bin) {
            $snapshot_bin = Bin::with([
                'site',
                'type_paper', 'type_paper.client_price', 'type_paper.recycle_price',
                'type_fabric', 'type_fabric.client_price', 'type_fabric.recycle_price',
            ])->where('id', $bin->id)->first();

            $snapshot_bin->types_snapshot = [];
            return $snapshot_bin;
        });
        $order->save();
    }
}
