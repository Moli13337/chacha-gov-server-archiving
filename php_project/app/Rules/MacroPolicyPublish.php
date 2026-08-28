<?php
/**
 * Created by PhpStorm.
 * User: Ebychu
 * Date: 19/5/10
 * Time: 下午3:07
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class MacroPolicyPublish implements Rule
{

    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function passes($attribute, $value)
    {
        if ($value == PUBLISH_STATUS['yes'] && empty($this->params)) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return trans('validation.custom.publish_status.publish');
    }
}