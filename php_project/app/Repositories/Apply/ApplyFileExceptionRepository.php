<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/2
 * Time: 14:12
 */

namespace App\Repositories\Apply;


use App\Models\ApplyFileExceptionModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApplyFileExceptionRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApplyFileExceptionModel::class;
    }

    public function getByFile($where, $column=['*'])
    {
        $res = $this->model->select($column)->where($where)->first();
        return empty($res)?[]:$res->toArray();
    }

    /**
     * FUNCTION_NAME : refreshApply
     * author : jp
     * 清除指定apply_id的 异常 信息
     * @param $apply_id
     * @return mixed
     */
    public function refreshApply($apply_id)
    {
        $where = [
            'apply_id' => $apply_id,
        ];
        return $this->model->where($where)->delete();
    }

    /**
     * FUNCTION_NAME : refreshApplyFile
     * author : jp
     * 删除发票 指定文件id
     * @param $fileIds
     * @return bool
     */
    public function refreshApplyFile($fileIds)
    {
        if (empty($fileIds)) {
            return false;
        }
        return $this->model->whereIn('apply_file_id', $fileIds)->delete();
    }
}