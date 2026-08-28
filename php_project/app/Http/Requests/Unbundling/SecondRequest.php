<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/22
 * Time: 10:53
 */

namespace App\Http\Requests\Unbundling;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class SecondRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'mobile' => ['required', 'integer', new Mobile(), Rule::unique(UserModel::TABLE_NAME)->ignore(getLoginHome('id'))
                ->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'code' => ['required', 'string', new SmsCode($this->input('mobile'))]
        ];
    }

    public function messages()
    {
        return [
            'mobile.unique' => trans('validation.custom.mobile.change'),
        ];
    }
}