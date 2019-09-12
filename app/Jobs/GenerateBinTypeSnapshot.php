<?php

namespace App\Jobs;

use App\Models\Bin;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;


class GenerateBinTypeSnapshot
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bin;

    public function __construct(Bin $bin)
    {
        $this->bin = $bin;
    }

    public function handle()
    {

        $bin = $this->bin;
        $bin->types_snapshot = DB::transaction(function () use ($bin) {
            $type_fabric = $bin->type_fabric()->with(['client_price', 'clean_price'])->first();
            $type_paper = $bin->type_paper()->with(['client_price', 'clean_price'])->first();
            return ['type_fabric' => $type_fabric, 'type_paper' => $type_paper];
        });
        $bin->save();

    }
}
