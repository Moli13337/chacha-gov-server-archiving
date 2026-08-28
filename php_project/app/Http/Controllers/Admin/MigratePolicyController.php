<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 14:13
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Models\PolicyModel;
use App\Repositories\OriginalPolicy\OriginalBigDataRepository;
use App\Repositories\OriginalPolicy\OriginalPolicyConclusionRepository;
use App\Repositories\OriginalPolicy\OriginalPolicyDetailRepository;
use App\Repositories\OriginalPolicy\OriginalPolicyGovRepository;
use App\Repositories\OriginalPolicy\OriginalPolicyItemRepository;
use App\Repositories\OriginalPolicy\OriginalPolicyRepository;
use App\Repositories\Policy\BigDataRepository;
use App\Repositories\Policy\PolicyRepository;
use App\Support\Collection;
use App\Support\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigratePolicyController extends Controller
{

    protected $originalPolicyRepository;
    protected $originalPolicyDetailRepository;
    protected $originalPolicyConclusionRepository;
    protected $originalPolicyItemRepository;
    protected $originalPolicyGovRepository;
    protected $originalBigDataRepository;
    protected $policyRepository;
    protected $bigDataRepository;
    protected $http;

    public function __construct(OriginalPolicyRepository $originalPolicyRepository,
                                OriginalPolicyDetailRepository $originalPolicyDetailRepository,
                                OriginalPolicyConclusionRepository $originalPolicyConclusionRepository,
                                OriginalPolicyItemRepository $originalPolicyItemRepository,
                                OriginalPolicyGovRepository $originalPolicyGovRepository,
                                OriginalBigDataRepository $originalBigDataRepository,
                                PolicyRepository $policyRepository,
                                BigDataRepository $bigDataRepository)
    {
        $this->originalPolicyRepository = $originalPolicyRepository;
        $this->originalPolicyDetailRepository = $originalPolicyDetailRepository;
        $this->originalPolicyConclusionRepository = $originalPolicyConclusionRepository;
        $this->originalPolicyItemRepository = $originalPolicyItemRepository;
        $this->originalPolicyGovRepository = $originalPolicyGovRepository;
        $this->originalBigDataRepository = $originalBigDataRepository;
        $this->policyRepository = $policyRepository;
        $this->bigDataRepository = $bigDataRepository;
    }

    public function migrate()
    {
        activity()->disableLogging();

        // 全量更新
        $this->bigData();
        $this->policy();
    }

    /**
     * FUNCTION_NAME : policy
     * author : jp
     * 迁移政策
     */
    public function policy()
    {
        $id = $this->policyRepository->originalLast();

        $data = null;
        do {
            $flag = false;
            echo memory_get_usage().PHP_EOL;
            // 节约内存
            $this->originalPolicyRepository->getOne($id, $data);
            echo memory_get_usage().PHP_EOL;
            if (!empty($data)) {
                $flag = true;
                $id = $data['policy_id'];
                echo memory_get_usage().PHP_EOL;
                echo $data['policy_id'] . PHP_EOL;
                try {
                    DB::beginTransaction();
                    $this->policyRepository->migrate($data);
                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollBack();
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                }
                $data = null;

            }
        } while ($flag);
        Log::info('migrate policy complete');
    }

    /**
     * FUNCTION_NAME : bigData
     * author : jp
     * 迁移big_data
     */
    public function bigData()
    {
        $id = $this->bigDataRepository->originalLast();
        $time = returnCreatedUpdatedAt();
        // 获取big_data_original有几张分表
        $sharding = $this->originalBigDataRepository->shardingAll();
        $end = array_pop($sharding);
        array_push($sharding, $end);
        do {
            $current = $this->originalBigDataRepository->getSharding($id);
            // 没有分表了 跳出
            if ($id > 0 && !in_array($this->originalBigDataRepository->getSharding($id), $sharding)) {
                break;
            }
            $data = $this->originalBigDataRepository->getBatch($id, 10);
            $data = $data->toArray();
            if (!empty($data)) {
                $except = [
                    'big_data_id',
                    'text',
                    'src',
                    'src_web',
                    'src_url',
                    'type',
                    'policy_type',
                    'status',
                    'create_time',
                    'update_time',
                ];
                foreach ($data as $key => &$value) {
                    $id = $value['big_data_id'];
                    $value['original_big_data_id'] = $value['big_data_id'];
                    $value['content'] = $value['text'];
                    $value['source'] = $value['src'];
                    $value['source_web'] = $value['src_web'];
                    $value['source_url'] = $value['src_url'];
                    $value['obj_type'] = $value['policy_type'];
                    $value['pub_time'] = empty($value['pub_time']) ? 0 : strtotime($value['pub_time']);
                    $value = array_except($value, $except);
                    $value = array_merge($value, $time);
                }
                try {
                    $this->bigDataRepository->storeBatch($data);
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::error('migrate ');
                }
            }
            if ($id > 0 && empty($data) && $end != $current) {
                $id = $this->originalBigDataRepository->initId($current+1);
                // 生成假的数据
                $data = [1];
            } else {
                $id += 1;
            }
            echo $id.PHP_EOL;
            echo memory_get_usage().PHP_EOL;

        } while (!empty($data));
        Log::info('migrate big_data complete');

    }

    /**
     * FUNCTION_NAME : incrementBigData
     * author : jp
     * big_data 增量
     * @param $id
     * @throws \App\Exceptions\CodeException
     */
    public function incrementBigData()
    {
        $id = $this->bigDataRepository->originalLast();

        $this->http = new Http();
        $this->chaChaAuth();
        $time = returnCreatedUpdatedAt();
        $migrateUrl= '/migrate_big_data/migrate';
        $params = [
            'big_data_id' => $id
        ];
        $data = $this->http->httpRequest(env('CHACHA_URL').$migrateUrl, $params, 'GET');
        if (empty($data['data'])) {
            return;
        }
        $data = $data['data'];
        $except = [
            'big_data_id',
            'text',
            'src',
            'src_web',
            'src_url',
            'type',
            'policy_type',
            'status',
            'create_time',
            'update_time',
        ];
        foreach ($data as $key => &$value) {
            $value['original_big_data_id'] = $value['big_data_id'];
            $value['content'] = $value['text'];
            $value['source'] = $value['src'];
            $value['source_web'] = $value['src_web'];
            $value['source_url'] = $value['src_url'];
            $value['obj_type'] = $value['policy_type'];
            $value['pub_time'] = empty($value['pub_time']) ? 0 : strtotime($value['pub_time']);
            $value = array_except($value, $except);
            $value = array_merge($value, $time);
        }
        try {
            $this->bigDataRepository->storeBatch($data);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('migrate policy');
        }

    }

    /**
     * FUNCTION_NAME : chaChaAuth
     * author : jp
     * 政策通权限
     */
    public function chaChaAuth()
    {
        $time = time();
        $token = md5(env('CHACHA_APPID').$time.env('CHACHA_APPKEY'));
        $header = [
            'Token' => $token,
            'Timestamp' => $time,
            'Appid' => env('CHACHA_APPID')
        ];

        $this->http->setHeaders($header);
    }
}