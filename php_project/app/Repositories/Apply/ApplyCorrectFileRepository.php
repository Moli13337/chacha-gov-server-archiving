<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 1:03
 */

namespace App\Repositories\Apply;


use App\Models\ApplyCorrectFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApplyCorrectFileRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return ApplyCorrectFileModel::class;
    }

    public function deleteFile($where)
    {
        return $this->model->where($where)->delete();
    }


    /**
     * FUNCTION_NAME : getContent
     * author : jp
     * 查出用户提交的内容
     * @param $where ['apply_id', 'correct_id']
     * @return mixed
     */
    public function getContent($where)
    {
        return $this->model->where($where)->get()->toArray();
    }

    /**
     * FUNCTION_NAME : getChangeContent
     * author : jp
     * 查出变化的内容
     * @param $where
     * @return mixed
     */
    public function getChangeContent($where)
    {
        $type = [
            APPLY_CORRECT_FILE_TYPE['created'],
            APPLY_CORRECT_FILE_TYPE['deleted'],
        ];
        return $this->model->where($where)->whereIn('correct_type', $type)->get()->toArray();

    }

    public function updateByWhere($where, $data)
    {
        return $this->model->where($where)->update($data);
    }

}