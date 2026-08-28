<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/7
 * Time: 11:10
 */

namespace App\Repositories\Steward;


use App\Models\Steward\StewardUserOpinionFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class StewardUserOpinionFileRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return StewardUserOpinionFileModel::class;
    }
}