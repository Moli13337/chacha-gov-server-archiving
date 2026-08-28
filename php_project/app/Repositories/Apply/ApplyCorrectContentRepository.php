<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 1:03
 */

namespace App\Repositories\Apply;


use App\Models\ApplyCorrectContentModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApplyCorrectContentRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return ApplyCorrectContentModel::class;
    }

    public function selfUpdateOrCreate($where, $arr)
    {
        $this->model->updateOrCreate($where, $arr);
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


}