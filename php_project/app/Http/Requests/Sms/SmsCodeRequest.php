<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Sms;


use App\Http\Requests\BaseFormRequest;
use App\Rules\Captcha;
use App\Rules\Mobile;
use Illuminate\Validation\Rule;

class SmsCodeRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'mobile' => ['required', 'integer', new Mobile()],
            'key' => ['required', 'string'],
            'captcha' => ['required', new Captcha($this->input('key'))],
            'tag' => ['required', 'integer', Rule::in(HOME_SMS_CODE)]
        ];
    }
}