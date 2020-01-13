<?php

namespace App\Console;

use App\Jobs\SendSms;
use App\Models\RecyclerPayment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('backup:run --disable-notifications --only-db')->twiceDaily(1, 13)->runInBackground(); // 定时备份数据库
        $schedule->exec(base_path() . '/bin/laravels reload')->twiceDaily(2, 14)->after(function () {
            info("Schedule Laravel-s reload");
        });

        //        $schedule->call(function () {
        //
        //            if (app()->environment('production'))
        //            {
        //                $phones = [
        //                    '18600982820',
        //                    '18363928677',//李飞
        //                    '18561386786',//郎丰江
        //                    '15550809002',//李千千
        //                    '15154152876',//周金芳
        //                    '18366268341',//李俊
        //                ];
        //                $r_payment = RecyclerPayment::whereBetween('paid_at', [
        //                    now()->startOfDay(),// start
        //                    now()->endOfDay(),// end
        //                ])->get();
        //
        //                if ($r_payment->isEmpty())
        //                {
        //                    $date = now()->toDateTimeString();
        //                    $content = "今天截止到 {$date},还未进行微信支付充值,收到短信后前往小黑点回收员充值";
        //                    foreach ($phones as $v)
        //                    {
        //                        SendSms::dispatch($v, 'SMS_180961787', $content, [
        //                            'date' => $date
        //                        ]);
        //                    }
        //
        //                }
        //            }
        //        })->dailyAt('19:00');
    }

    /**
     * Register the commands for the application.
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
