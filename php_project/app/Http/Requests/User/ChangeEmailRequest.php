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
use App\Rules\Password;
use Illuminate\Validation\Rule;

class ChangeEmailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', Rule::unique(UserModel::TABLE_NAME)->ignore(getLoginHome('id'))
                ->whereNull('deleted_at')],
        ];
    }
}