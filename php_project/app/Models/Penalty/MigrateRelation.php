<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/9
 * Time: 15:35
 */

namespace App\Models\Penalty;


trait MigrateRelation
{
    public function migrate()
    {
        return $this->hasOne(MigrateModel::class, 'register_no');
    }
}