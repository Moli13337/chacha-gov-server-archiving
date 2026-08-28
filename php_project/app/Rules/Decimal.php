<?php
/**
 * Created by PhpStorm.
 * User: Ebychu
 * Date: 19/5/10
 * Time: 下午3:07
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class Decimal implements Rule
{

    protected $params;

    public function __construct($left = 10,$right = 2 )
    {
        $this->params[0] = $left;
        $this->params[1] = $right;
    }

    public function passes($attribute, $value)
    {
        return preg_match("/^[0-9]{1,{$this->params[0]}}(\.[0-9]{1,{$this->params[1]}}){0,1}$/", $value);
    }

    public function message()
    {
        return trans('validation.decimal');
    }
}