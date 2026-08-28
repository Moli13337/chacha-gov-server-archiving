<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/13
 * Time: 10:15
 */

namespace App\Models\Traits;


trait IsNewPublishTrait
{

    public function getIsNewAttribute()
    {
        if (!isset($this->attributes['publish_time']) || empty($this->attributes['publish_time'])) {
            return 0;
        }

        return ($this->attributes['publish_time'] < (time() -  7*24*60*60)) ? 0 : 1;
    }
}