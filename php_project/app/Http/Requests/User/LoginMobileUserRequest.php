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
use App\Rules\Mobile;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class LoginMobileUserRequest extends BaseFormRequest
{
    public function rules()
    {
        $table_name = UserModel::TABLE_NAME;
        return [
            'mobile' => ['required', 'integer', new Mobile(), Rule::exists($table_name)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'type' => ['nullable', 'integer', Rule::in(array_values(LOGIN_TYPE))],
            'code' => ['required', 'string', new SmsCode($this->input('mobile'))]
        ];
    }
}