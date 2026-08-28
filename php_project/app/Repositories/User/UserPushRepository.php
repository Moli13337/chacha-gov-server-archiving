<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/12
 * Time: 9:59
 */

namespace App\Repositories\User;


use App\Models\UserPushModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserPushRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return UserPushModel::class;
    }
}