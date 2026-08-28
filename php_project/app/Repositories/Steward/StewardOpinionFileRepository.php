<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Steward;


use App\Models\Steward\StewardOpinionFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class StewardOpinionFileRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return StewardOpinionFileModel::class;
    }

    public function getList($agent_notify_id, $column = ['*'])
    {
        return $this->model->select($column)->where('steward_opinion_id', $agent_notify_id)->get()->toArray();
    }

}