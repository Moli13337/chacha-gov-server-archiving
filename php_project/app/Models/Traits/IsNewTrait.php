<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/13
 * Time: 10:15
 */

namespace App\Models\Traits;


trait IsNewTrait
{

    public function getIsNewAttribute()
    {
        if (!isset($this->attributes['created_at']) || empty($this->attributes['created_at'])) {
            return 0;
        }

        return ($this->attributes['created_at'] < (time() -  7*24*60*60)) ? 0 : 1;
    }
}