<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/19
 * Time: 17:11
 */

namespace App\Models;


trait BelongPublishStaff
{
    public function publishStaff(){
        return $this->belongsTo(StaffModel::class, PUBLISH_STAFF_ID)
            ->select(['id','name'])
            ->withDefault([
                'id' => 0,
                'name' => '',
            ]);
    }

}