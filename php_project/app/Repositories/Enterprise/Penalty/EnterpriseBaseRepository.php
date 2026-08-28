<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 15:53
 */

namespace App\Repositories\Enterprise\Penalty;


use App\Models\Penalty\AjjPenaltyModel;
use App\Models\Penalty\EnterpriseBaseModel;
use App\Repositories\BaseRepository;

class EnterpriseBaseRepository extends BaseRepository
{

    public function model()
    {
        return EnterpriseBaseModel::class;
    }

    public function list($type, $limit)
    {
        return $this->model->where('IS_DELETE', 0)->where('ENT_TYPE', '!=', 9600)->whereNotNull('IDNO')->whereDoesntHave('migrate', function ($query) use ($type) {
            $query->where('type', $type);
        })->limit($limit)->get()->toArray();
    }



}