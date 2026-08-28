<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 15:28
 */

namespace App\Repositories\Steward;


use App\Models\Steward\StewardInformationFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class StewardInformationFileRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return StewardInformationFileModel::class;
    }

    public function getList($agent_notify_id, $column = ['*'])
    {
        return $this->model->select($column)->where('steward_info_id', $agent_notify_id)->get()->toArray();
    }
}