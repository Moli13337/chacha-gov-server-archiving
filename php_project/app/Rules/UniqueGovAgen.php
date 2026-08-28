<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/16
 * Time: 15:52
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class UniqueGovAgen implements Rule
{
    protected $params;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $tmp = [];
        foreach ($value as $key => $val) {
            if (!isset($val['gov_agen_first']) || !isset($val['gov_agen_second'])) {
                continue;
            }
            if (in_array($val['gov_agen_first'].'-'.$val['gov_agen_second'], $tmp)) {
                return false;
            }
            $tmp[] = $val['gov_agen_first'].'-'.$val['gov_agen_second'];
        }
        return true;
    }

    public function message()
    {
        return trans('validation.custom.gov_agen.unique');
    }
}