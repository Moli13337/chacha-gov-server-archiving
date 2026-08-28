<?php

namespace App\Providers;


use App\Models\PolicyModel;
use App\Observers\PolicyObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',

        ],
        // sql
        'Illuminate\Database\Events\QueryExecuted' => [
            'App\Listeners\QueryListener',
        ],
        // 政策行业编辑
        'App\Events\IndustryChange' => [
            'App\Listeners\IndustryChangeListener'
        ],
        // 政策关联
        'App\Events\PolicyRelation' => [
            'App\Listeners\PolicyRelationListener'
        ],
//        // 政策批量删除
//        'App\Events\PolicyBatchDelete' => [
//            'App\Listeners\PolicyBatchDeleteListener'
//        ],
        // 计算中介评分
        'App\Events\ComputeStars' => [
            'App\Listeners\ComputeStarsListener'
        ],
        // 打包文件
        'App\Events\ZipCreate' => [
            'App\Listeners\ZipCreateListener'
        ],
        // 打包文件
        'App\Events\ApplyZipFileCreate' => [
            'App\Listeners\ApplyZipFileCreateListener'
        ],
        // 申报详情
        'App\Events\ApplyPdfCreate' => [
            'App\Listeners\ApplyPdfCreateListener'
        ],

        // 申报表
        'App\Events\ApplyFormPdfCreate' => [
            'App\Listeners\ApplyFormPdfCreateListener'
        ],
        // 批量上下架
        'App\Events\BatchPublish' => [
            'App\Listeners\BatchPublishListener'
        ],
        // 推荐
        'App\Events\Recommend' => [
            'App\Listeners\RecommendListener'
        ],
    ];


    // 注册日志事件订阅
    protected $subscribe = [
        'App\Listeners\LogEventSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
//        Log::class;
//        Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//        Log::info(json_encode($query));
//        });

        // 测试policy观察者
//        PolicyModel::observe(PolicyObserver::class);
    }
}
