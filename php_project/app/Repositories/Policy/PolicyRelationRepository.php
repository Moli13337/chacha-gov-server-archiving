<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\PolicyRelationModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyRelationRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyRelationModel::class;
    }

    public function storeBatch($data)
    {
        try {
            $this->model->insert($data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return true;
    }

    public function deleteObjID($id)
    {
        return $this->model->where('obj_id', $id)->OrWhere('obj_type_relation_id', $id)->delete();
    }
}