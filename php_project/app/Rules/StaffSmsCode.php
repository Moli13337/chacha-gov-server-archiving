<?php
/**
 * Created by PhpStorm.
 * User: Ebychu
 * Date: 19/5/10
 * Time: 下午3:07
 */

namespace App\Rules;


use App\Repositories\Staff\StaffCodeRepository;
use Illuminate\Contracts\Validation\Rule;

class StaffSmsCode implements Rule
{

    protected $params;

    public function __construct($mobile)
    {
        $this->params = $mobile;
    }

    public function passes($attribute, $value)
    {
        if (!preg_match("/^[0-9]{6}$/", $value)) {
            return false;
        }

        // 验证码校验
        $data['mobile'] = $this->params;
        $data['code'] = $value;
        $data['expire'] = CODE_EXPIRES;
        $resultCode = app(StaffCodeRepository::class)->checkCode($data);
        if (empty($resultCode) || $resultCode['code'] !== $data['code']) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return trans('validation.code');
    }
}