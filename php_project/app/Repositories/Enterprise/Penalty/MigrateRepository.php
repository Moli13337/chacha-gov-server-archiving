<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise\Penalty;


use App\Models\Penalty\MigrateModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class MigrateRepository extends BaseRepository
{
    use CommonRepository;
    
    public function model()
    {
        return MigrateModel::class;
    }

    public function storeBatch($data)
    {
        return $this->model->insert($data);
    }



}