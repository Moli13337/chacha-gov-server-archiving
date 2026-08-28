<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserModel;
use App\Rules\Captcha;
use App\Rules\Mobile;
use App\Rules\Password;
use Illuminate\Validation\Rule;

class LoginUserRequest extends BaseFormRequest
{
    public function rules()
    {
        $table_name = UserModel::TABLE_NAME;
        return [
            'account' => ['required', 'string'],
            'password' => ['required', 'string', new Password()],
            'type' => ['nullable', 'integer', Rule::in(array_values(LOGIN_TYPE))],
            'key' => ['required', 'string'],
            'captcha' => ['required', new Captcha($this->input('key'))]
        ];
    }
}