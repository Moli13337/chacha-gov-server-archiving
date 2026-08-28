<?php

namespace App\Console\Commands;

use App\Common\Code;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Runtime\Scheduler;
use App\Support\Http;
use Composer\DependencyResolver\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise;

class DetectLive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect:live {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '探测请求存活';

    // 响应超时时间, 超过这段时间认为 php-fpm 已经僵死， kill php-fpm 由 supervisod 重新启动
    protected $timeout = 60;

    // 响应超过这个时间就要发送时间
    protected  $diffBase = 1;

    // 是否杀死进程php-fpm
    protected $isKill = false;

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
        //
        $rule = [
            'web-1' => 'web-1:8000',
            // 这里有web-2 变为了 data-crawler-1
            'data-crawler-1' => 'data-crawler-1:8000',
            'web-3' => 'web-3:8000',
        ];
        $target = '/api/detect/live';

        if ($this->option('name')) {
            $rule = array_only($rule, $this->option('name'));
        }

        if (empty($rule)) {
            throw new \Exception('name :'.$this->option('name').'不在 规则内');
        }

        // 是否kill 进程
        $this->isKill = count($rule) <= 1;

//        $this->detect($rule, $target);
        $time = time();

        $this->detectAsync($rule, $target);

        // TODO 未实现， hard: yield 怎样控制 http io 的实现
//        $scheduler = new Scheduler();
//        foreach ($rule as $key => $value) {
////            $scheduler->addTask(yield from $this->detectRuntime($key, $value, $target, $this->timeout, $this->isKill, $this->diffBase));
//            $scheduler->addTask($this->task($key, $value, $target));
//            echo '--';
//        }
//        $scheduler->run();
    }

    public function task($key, $value, $target)
    {
        yield from $this->detectRuntime($key, $value, $target, $this->timeout, $this->isKill, $this->diffBase);
    }

    public function send($content)
    {
        $url = 'https://oapi.dingtalk.com/robot/send?access_token=6b5ca67502dfc73b2923bdcb3c0586b07364a3970e7f03f6a8733aa1ed9114cc';
        $data = [
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
         curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * FUNCTION_NAME : detect
     * author : jp
     * 健康状况检查
     * @param $rule
     * @param $target
     */
    public function detect($rule, $target)
    {
        $http = new Http();
        foreach ($rule as $k => $v) {
            $start = time();
            $str = $k . ' target url : ' . $target .'.';
            try {
                $res = $http->httpRequest($v.$target, [], 'GET', ['timeout' => $this->timeout] );
            } catch (CodeException $e) {
                $this->killFpm($e->getMessage(), $this->isKill);
                $this->send($str . ' ' . $e->getMessage());
                continue;
            } catch (\Exception $e) {
                $this->send($str . ' ' .$e->getMessage());
                continue;
            }
            $end = time();
            $diff = $end - $start;
            if ( $diff > $this->diffBase) {
                $this->send($str . ' 响应超过 1 s, 响应时间： '. $diff);
            } elseif (!is_array($res) || empty($res['code'])) {
                $this->send($str . ' 请求错误. output: ' . print_r($res, true));
            }

        }
    }

    /**
     * FUNCTION_NAME : detectRuntime
     * author : jp
     * 协程 检查
     * @param $name
     * @param $host
     * @param $target
     * @param $timeout
     * @param $diffBase
     * @param $isKill
     * @return \Generator
     */
    protected function detectRuntime($name, $host, $target, $timeout, $diffBase, $isKill)
    {
        $http = new Http();
        $start = time();
        $str = $name . ' target url : ' . $target .'.';
        $options = [
            'timeout' => $timeout,
        ] ;
        try {
            $res = $http->httpRequest($host.$target, [], 'GET', [
                'timeout' => $timeout,
                'synchronous' => true
            ] );
            yield;
        } catch (CodeException $e) {
            echo $name . PHP_EOL;
            $this->killFpm($e->getMessage(), $isKill);
            $this->send($str . ' ' . $e->getMessage());
            return ;
        } catch (\Exception $e) {
            $this->send($str . ' ' .$e->getMessage());
            return ;
        }
        $end = time();
        $diff = $end - $start;
        if ( $diff > $diffBase) {
            $this->send($str . ' 响应超过 1 s, 响应时间： '. $diff . ' s');
        } elseif (!is_array($res) || empty($res['code'])) {
            $this->send($str . ' 请求错误. output: ' . print_r($res, true));
        }
        yield;

    }

    /**
     * FUNCTION_NAME : detectAsync
     * author : jp
     * 异步请求
     * @param $rule
     * @param $target
     * @throws \Throwable
     */
    public function detectAsync($rule, $target)
    {
        $client = new  Client();

        $promise = [];

        $op = ['timeout' => $this->timeout] ;

        $start  = time();
        foreach ($rule as $k => $v) {
            $str = $k  . ' target url : ' . $target .'.';
            $promise[$k] = $client->getAsync( $v.$target, $op)->then(
                function (ResponseInterface $res) use ($start, $str) {
                    $this->dealSuccess(json_decode($res->getBody(),true), $start, $this->diffBase, $str);
                },
                function (RequestException $e) use($str, $k) {
                    $this->killFpm($e->getMessage(), $this->isKill, $str, $k);
                });
        }

        $results = Promise\unwrap($promise);
        // or
//        $results = Promise\settle($promise)->wait();
    }

    /**
     * FUNCTION_NAME : killFpm
     * author : jp
     * kill fpm
     * @param $message
     * @param $isKill
     * @param $str
     * @param $name
     */
    public function killFpm($message, $isKill, $str,$name)
    {
        // 执行 kill
        if ($isKill) {
            return;
        }
        // 超时的标识
        $timeout_str = [
            'cURL error 28',
            '504 Gateway Time-out',
        ];
        // 需要执行的命令
//        $command = "ps -ef |grep php-fpm |grep -v grep |awk '{print $2}' |xargs kill -9";
        $command = '/data/orp/sh-crond/all-php-check.sh '.$name;
        foreach ($timeout_str as $kt => $vt) {
            if (stripos($message, $vt) !== false) {
                exec($command);
                break;
            }
        }
        $this->send($str . ' ' . $message);

    }

    public function dealSuccess($res, $start ,$diffBase, $str)
    {
        $diff = time() - $start;
        if ( $diff > $diffBase) {
            $this->send($str . ' 响应超过 1 s, 响应时间： '. $diff . ' s');
        } elseif (!is_array($res) || empty($res['code'])) {
            $this->send($str . ' 请求错误. output: ' . print_r($res, true));
        }
    }

}
