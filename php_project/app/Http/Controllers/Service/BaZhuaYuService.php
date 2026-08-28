<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/27
 * Time: 18:02
 */

namespace App\Http\Controllers\Service;


use App\Support\Http;
use Doctrine\Common\Cache\Cache;

class BaZhuaYuService extends BaseService
{

    protected $username;
    protected $password;

    protected $baseUrl;

    // token
    protected $tokenUrl = '/token';
    // 获取所有任务组
    protected $groupUrl = '/api/TaskGroup';
    // 获取任务组中的任务
    protected $taskUrl = '/api/Task';
    // 获取任务数据
    protected $getDataUrl = '/api/alldata/GetDataOfTaskByOffset';
    // 导出任务数据
    protected $exportDataUrl = '/api/notexportdata/gettop';
    // 标记任务数据为已导出
    protected $updateDataUrl = '/api/notexportdata/update';
    // 清空任务数据
    protected $removeDataUrl = '/api/task/RemoveDataByTaskId';

    protected $expire_time = 86399;

    protected $http;

    public function __construct()
    {
        $this->username = env('BAZHUAYU_USERNAME');
        $this->password = env('BAZHUAYU_PASSWORD');

        $this->baseUrl = env('BAZHUAYU_URL');
        $this->http = new Http();
    }

    public function getToken()
    {
        $token = Cache::get(REDIS_BAZHUAYU_TOKEN, '');
        if (empty($token)) {
            $params = [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ];

            $out = $this->http->httpRequest($this->baseUrl.$this->tokenUrl, $params, 'POST');

            if (!empty($out['access_token']) && !empty($out['token_type']) && !empty($out['expires_in'])) {
                $token = $out['token_type'].' '.$out['access_token'];
                Cache::put(REDIS_BAZHUAYU_TOKEN, $token, $this->expire_time);
            }
        }
        $headers = [
            'Authorization' => $token
        ];
        $this->http->setHeaders($headers);

        return $token;
    }

    /**
     * FUNCTION_NAME : refreshTask
     * author : jp
     * 刷新缓存中的任务组
     * @return array
     * @throws \App\Exceptions\CodeException
     */
    public function refreshTask()
    {
        $this->getToken();

        $out = $this->http->httpRequest($this->baseUrl . $this->groupUrl, [], 'GET');

        if(isset($out['error']) && ('success' == $out['error'])) {
            $data = $out['data'];
            Cache::put(REDIS_BAZHUAYU_GROUP, $data, $this->expire_time);
            return $data;
        }
        return [];
    }

    /**
     * FUNCTION_NAME : getTaskGroup
     * author : jp
     * 获取所有任务组
     * @return array
     * @throws \App\Exceptions\CodeException
     */
    public function getTaskGroup()
    {
        $group = Cache::get(REDIS_BAZHUAYU_GROUP, []);

        if (empty($group)) {
            $group = $this->refreshTask();
        }
        return $group;
    }

    public function getTask($task_group_id)
    {
        $this->getToken();
        $params = [
            'taskGroupId' => $task_group_id
        ];

        $out = $this->http->httpRequest($this->baseUrl . $this->taskUrl, $params, 'GET');
        if(isset($out['error']) && ('success' == $out['error']))
        {
            return $out['data'];
        }
        return [];
    }

    public function exportDataAmount($task_id, $tag)
    {
        $data = $this->exportData($task_id, $tag);
        if(is_string($data))
        {
            log_message('error', 'no-msg get_export_data_amount error, out:'.var_export($data, TRUE));
            return $data;
        }
        $total = $data['total'];
        unset($data);
        return $total;
    }

}