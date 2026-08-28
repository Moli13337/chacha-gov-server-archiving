<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Rules\Password;

class ChangePwdRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'old_password' => ['required', 'string', new Password()],
            'password' => ['required', 'string', new Password(),'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}