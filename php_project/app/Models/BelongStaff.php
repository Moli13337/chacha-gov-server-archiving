<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/19
 * Time: 17:11
 */

namespace App\Models;


trait BelongStaff
{
    public function staff(){
        return $this->belongsTo(StaffModel::class, CREATED_STAFF_ID)
            ->select(['id','name', 'mobile'])
            ->withDefault([
                'id' => 0,
                'name' => '',
                'mobile' => '',
            ]);
    }

}