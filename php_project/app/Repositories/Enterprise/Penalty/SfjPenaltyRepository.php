<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 15:53
 */

namespace App\Repositories\Enterprise\Penalty;


use App\Models\Penalty\SfjPenaltyModel;
use App\Repositories\BaseRepository;

class SfjPenaltyRepository extends BaseRepository
{
    use ListPenalty;

    public function model()
    {
        return SfjPenaltyModel::class;
    }
}