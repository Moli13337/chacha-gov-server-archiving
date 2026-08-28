<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/22
 * Time: 10:53
 */

namespace App\Http\Requests\Unbundling;


use App\Http\Requests\BaseFormRequest;
use App\Rules\SmsCode;

class FirstRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'code' => ['required', 'string', new SmsCode(getLoginHome('mobile'))]
        ];
    }
}