<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 09:45
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\Password;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends BaseFormRequest
{
    public function rules()
    {
        $table_name = UserModel::TABLE_NAME;
        return [
            'name' => ['required', 'string', 'max:20'],
            'mobile' => ['required', 'integer', new Mobile(), Rule::unique($table_name)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'type' => ['nullable', 'integer', Rule::in(array_values(LOGIN_TYPE))],
            'code' => ['required', 'string', new SmsCode($this->input('mobile'))],
            'uid' => ['required', 'string', Rule::unique($table_name)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
        ];
    }
}