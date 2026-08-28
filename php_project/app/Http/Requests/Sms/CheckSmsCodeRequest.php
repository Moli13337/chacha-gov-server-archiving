<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Sms;


use App\Http\Requests\BaseFormRequest;
use App\Rules\Mobile;
use App\Rules\SmsCode;

class CheckSmsCodeRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'mobile' => ['required', 'integer', new Mobile()],
            'code' => ['required', 'string', new SmsCode($this->input('mobile'))],
        ];
    }
}