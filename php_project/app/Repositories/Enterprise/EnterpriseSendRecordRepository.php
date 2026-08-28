<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Models\EnterpriseSendRecordModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseSendRecordRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseSendRecordModel::class;
    }


}