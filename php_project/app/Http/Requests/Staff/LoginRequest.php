<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 13:54
 */

namespace App\Http\Requests\Staff;


use App\Http\Requests\BaseFormRequest;
use App\Models\StaffModel;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class LoginRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'uid' => ['required', 'string'],
        ];
    }
}