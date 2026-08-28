<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/21
 * Time: 10:24
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\Password;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class AdminChangeMobileRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(UserModel::TABLE_NAME)->whereNull('deleted_at')],
            'mobile' => ['required', 'integer', new Mobile(), Rule::unique(UserModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
        ];
    }

    public function messages()
    {
        return [
            'mobile.unique' => trans('validation.custom.mobile.change'),
        ];
    }
}