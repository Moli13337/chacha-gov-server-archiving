<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/1
 * Time: 15:51
 */

namespace App\Http\Controllers\Service;


use App\Repositories\Policy\PolicySummarizeDirectionRepository;
use App\Repositories\Policy\PolicySummarizeRepository;
use App\Support\Collection;

class SummarizeService extends BaseService
{

    protected $policySummarizeRepository;
    protected $policySummarizeDirectionRepository;

    public function __construct(PolicySummarizeRepository $policySummarizeRepository,
                                PolicySummarizeDirectionRepository $policySummarizeDirectionRepository)
    {

        $this->policySummarizeRepository = $policySummarizeRepository;
        $this->policySummarizeDirectionRepository = $policySummarizeDirectionRepository;
    }

    /**
     * FUNCTION_NAME : relationSummarizeInsert
     * author : jp
     * 新增概述
     * @param $data
     * @param $policy_id
     * @throws \App\Exceptions\QueryException
     */
    public function relationSummarizeInsert($data, $policy_id)
    {

        // 第一步先写 概述方向
        // 第二写 概述
        $white_direction = [
            'name'
        ];
        $white = [
            'title',
            'content',
            'direction_id'
        ];

        $insert_batch = [];

        foreach ($data as $k => $v) {
            $direction = [];
            $direction = Collection::filter($white_direction, $v);
            $direction['policy_id'] = $policy_id;
            $res = $this->policySummarizeDirectionRepository->storeRepository($direction);
            $direction_id = $res['id'];

            foreach ($v['summarize'] as $key => $value) {
                $value = Collection::filter($white, $value);
                $value['policy_id'] = $policy_id;
                $value['direction_id'] = $direction_id;
                $insert_batch[] = array_merge($value, returnCreatedUpdatedAt());
            }
        }

        if (!empty($insert_batch)) {
            $this->policySummarizeRepository->storeBatch($insert_batch);
        }
    }

    public function relationSummarizeUpdate($data, $policy_id)
    {
        $this->policySummarizeRepository->deleteByPolicyId($policy_id);
        $this->policySummarizeDirectionRepository->deleteByPolicyId($policy_id);
        if (!empty($data)) {
            $this->relationSummarizeInsert($data, $policy_id);
        }

    }


    /**
     * FUNCTION_NAME : getSummarize
     * author : jp
     * 为政策详情 组装 概述
     * @param $policy_id
     * @param $data
     * @return array
     */
    public function getSummarize($policy_id, $data)
    {
        $summarize = $this->policySummarizeRepository->getByPolicyId($policy_id);
        // 为每一个方向装上summarize
        if (empty($data) || empty($summarize)) {
            return $data;
        }
        $direction = array_column($data, 'id');
        $direction = array_flip($direction);
        $data = array_map(function ($item) {
            $item = Collection::filter(['id', 'name'], $item);
            return $item;
        }, $data);

        foreach ($summarize as $key => $value) {
            if (isset($direction[$value['direction_id']])) {
                $data[$direction[$value['direction_id']]]['summarize'][] = $value;
            }
        }

        return $data;
    }

    /**
     * FUNCTION_NAME : getCSummarize
     * author : jp
     * 客户端 概述
     * @param $policy_id
     * @param $data
     * @return array
     */
    public function getClientSummarize($policy_id, $data)
    {
        $summarize = $this->policySummarizeRepository->getByPolicyId($policy_id);
        // 为每一个方向装上summarize
        if (empty($data) || empty($summarize)) {
            return $data;
        }
        $direction = array_column($data, 'id');
        $direction = array_flip($direction);
        $data = array_map(function ($item) {
            $item['summarize'] = [];
            $item = Collection::filter(['id', 'name'], $item);
            return $item;
        }, $data);

        foreach ($summarize as $key => $value) {
            if (isset($direction[$value['direction_id']])) {
                $value = Collection::filter(['direction_id', 'title', 'content'], $value);
                $data[$direction[$value['direction_id']]]['summarize'][] = $value;
            }
        }

        return $data;
    }


}