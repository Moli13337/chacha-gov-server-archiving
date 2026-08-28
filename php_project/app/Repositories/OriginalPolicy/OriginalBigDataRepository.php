<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 15:03
 */

namespace App\Repositories\OriginalPolicy;


use App\Models\OriginalPolicy\OriginalBigDataModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OriginalBigDataRepository extends BaseRepository
{

    public function model()
    {
        return OriginalBigDataModel::class;
    }

    public function getBatch($id, $limit)
    {
        $res = $this->model->setTable($this->setSharding($id))
            ->where('status', 2)
            ->where('big_data_id', '>=',$id)
            ->orderBy('big_data_id', 'ASC')
            ->limit($limit)
            ->get();
        return $res;
    }

    public function getSharding($big_data_id)
    {
        return $sharding = (int)ceil($big_data_id / 1000000);
    }

    /**
     * FUNCTION_NAME : setSharding
     * author : jp
     * 原始表已经分表需要处理
     * @param int $big_data_id
     * @return string
     */
    public function setSharding(int $big_data_id) {
        $sharding = $this->getSharding($big_data_id);
        $sharding = empty($sharding) ? 1 : $sharding;
        return 'big_data_origin_'.$sharding;
    }

    public function shardingAll()
    {
        $prefix = 'big_data_origin_';
        $table_list = DB::connection('mysql_original_policy')->getDoctrineSchemaManager()->listTableNames();
        $sharding_arr = [];
        foreach ($table_list as $k => $v) {

            $sharding_arr[] = (int)str_replace($prefix, '', $v);
        }
        return array_filter($sharding_arr);

    }

    public function initId($sharding)
    {
        return ($sharding - 1) * 1000000 + 1;
    }
}