<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:07
 */

namespace App\Repositories\Apply;


use App\Models\ApprovalCoordinateRelationModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApprovalCoordinateRelationRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApprovalCoordinateRelationModel::class;
    }
}