<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/17
 * Time: 10:38
 */

namespace App\Observers;


use App\Models\PolicyModel;

class PolicyObserver
{

    public function updated(PolicyModel $model)
    {
//        dd(2224);
    }

    public function deleting(PolicyModel $model)
    {
//        dd(222);
    }

    public function deleted(PolicyModel $model)
    {
//        dd($model);
//        dd(222224444);
    }
}