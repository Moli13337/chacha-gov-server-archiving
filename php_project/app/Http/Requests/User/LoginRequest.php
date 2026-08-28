<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 10:12
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserModel;
use Illuminate\Validation\Rule;

class LoginRequest extends BaseFormRequest
{

    public function rules()
    {
        $table_name = UserModel::TABLE_NAME;
        return [
            'uid' => ['required', 'string'],
            'type' => ['nullable', 'integer', Rule::in(array_values(LOGIN_TYPE))],
        ];
    }
}