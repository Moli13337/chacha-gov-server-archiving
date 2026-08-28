<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/16
 * Time: 15:52
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class UniqueTitleSummarize implements Rule
{
    protected $params;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $title = [];
        foreach ($value as $key => $val) {
            // 检查名称重复
            if (is_array($val)) {
                if (isset($val['title']) && in_array($val['title'], $title)) {
                    return false;
                } elseif (isset($val['title'])) {
                    $title[] = $val['title'];
                }
            }

        }
        return true;
    }

    public function message()
    {
        return trans('validation.custom.summarize.title.unique');
    }
}