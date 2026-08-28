<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/9
 * Time: 15:10
 */

namespace App\Repositories\Enterprise\Penalty;


trait ListPenalty
{
    public function listPenalty($type, $limit)
    {
        return $this->model->whereDoesntHave('migrate', function ($query) use ($type) {
            $query->where('type', $type);
            $query->where('IS_DELETE', '!=', 1);
        })->limit($limit)->get()->toArray();
    }
}