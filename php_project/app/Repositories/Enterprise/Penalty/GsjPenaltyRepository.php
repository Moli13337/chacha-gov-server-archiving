<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 15:53
 */

namespace App\Repositories\Enterprise\Penalty;


use App\Models\Penalty\GsjPenaltyModel;
use App\Repositories\BaseRepository;

class GsjPenaltyRepository extends BaseRepository
{
    use ListPenalty;

    public function model()
    {
        return GsjPenaltyModel::class;
    }
}