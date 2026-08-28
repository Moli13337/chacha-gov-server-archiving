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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //调度任务 导入税收  避免重复任务 后台运行 每个小时执行
        $schedule->command('tax:import')->hourly()->withoutOverlapping()->runInBackground();
        // 迁移企业数据 避免重复任务 后台运行 每天运行一次 凌晨 10 分
        $schedule->command('migrate:enterprise')->dailyAt('00:10')->withoutOverlapping()->runInBackground();
        // 申请表预处理 后台运行 5分钟一次
        $schedule->command('command:checkapply')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
        // 检查主审部门、协同部门、园区管委会的审批时间 1 3天提醒  后台运行 每天1次，9点开始执行
        $schedule->command('command:checkapproval')->dailyAt('09:00')->withoutOverlapping()->runInBackground();
        // 补充材料24小时到期提醒 后台运行 每天1次，9点开始执行
        $schedule->command('command:checkmaterial')->dailyAt('09:00')->withoutOverlapping()->runInBackground();
        // 推送短信发送 每分钟 启动一次
        $schedule->command('send:steward:push')->everyMinute()->withoutOverlapping()->runInBackground();
        // 推荐服务 避免重复任务 后台运行 每秒
        $schedule->command('cron:recommend')->cron('* * * * *')->withoutOverlapping()->runInBackground();
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
