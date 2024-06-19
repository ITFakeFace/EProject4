<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateDayOfLeave::class,
        Commands\ResetDayOfLeave::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Update day_of_leave + 1 lúc 1 giờ sáng ngày đầu tiên của tháng của tất cả nhân viên
        $schedule->command('update:cron')
                ->monthlyOn(1, '01:00')
                //->everyMinute()
                ->timezone('Asia/Ho_Chi_Minh');
    
        //Reset day_of_leave = 0 lúc 0 giờ sáng ngày đầu tiên của năm của tất cả nhân viên
        $schedule->command('reset:cron')
                ->yearly()
                ->timezone('Asia/Ho_Chi_Minh');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
