<?php
/**
 * Created by PhpStorm.
 * User: Ebychu
 * Date: 19/5/10
 * Time: 下午3:07
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{

    protected $params;

    public function __construct( )
    {

    }

    public function passes($attribute, $value)
    {
        return preg_match("/^[\w$@$!%#?&]{6,20}$/", $value);
    }

    public function message()
    {
        return trans('validation.custom.password.regex');
    }
}