<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class GithubUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Application From Github';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //从github 更新代码
        info('git pull');
        $path = base_path();
        $cmd = "cd $path && git pull";
        shell_exec($cmd);

        //Laravel-s reload
        info('Laravel-s reload');
        shell_exec("php $path/bin/laravels reload");
    }
}
