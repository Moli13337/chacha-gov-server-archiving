<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 15:03
 */

namespace App\Repositories\OriginalPolicy;


use App\Models\OriginalPolicy\OriginalPolicyGovModel;
use App\Repositories\BaseRepository;

class OriginalPolicyGovRepository extends BaseRepository
{

    public function model()
    {
        return OriginalPolicyGovModel::class;
    }
}