<?php

namespace App\Jobs;

use App\Models\Bin;
use App\Models\BinToken;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ClearBinToken implements ShouldQueue
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
        BinToken::where('bin_id', $bin->id)->delete();// 清空已有token
    }
}
