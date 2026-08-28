<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/16
 * Time: 15:52
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class UniqueDirection implements Rule
{
    protected $params;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $tmp = [];
        foreach ($value as $key => $val) {
            // 检查方向重复
            if (isset($val['name']) && in_array($val['name'], $tmp)) {
                return false;
            } elseif (isset($val['name'])) {
                $tmp[] = $val['name'];
            }
        }

        return true;
    }

    public function message()
    {
        return trans('validation.custom.summarize.unique');
    }
}