<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/9
 * Time: 15:52
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class Telephone implements Rule
{
    protected $params;

    public function __construct( )
    {

    }

    public function passes($attribute, $value)
    {
        return preg_match("/^(0?\d{2,3}-)?\d{7,8}$/", $value);
    }

    public function message()
    {
        return trans('validation.regex');
    }
}