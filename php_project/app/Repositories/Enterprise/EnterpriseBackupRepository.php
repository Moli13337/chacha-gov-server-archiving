<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Events\LogCommon;
use App\Exceptions\QueryException;
use App\Models\EnterpriseBackupModel;
use App\Models\EnterpriseTaxModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseBackupRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseBackupModel::class;
    }

    public function customUpdateOrCreate($key_no, $name, $content)
    {
        return $this->model->updateOrCreate(
            ['key_no' => $key_no],
            ['name' => $name, 'content' => $content]
            );
    }

    public function getByName($name)
    {
        $res =  $this->model->where('name', $name)->first(['content']);
        return empty($res) ? [] : json_decode($res->toArray()['content'], true);
    }

}