<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories;


use App\Models\OperationLogsModel;

class OperationLogsRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return OperationLogsModel::class;
    }
}