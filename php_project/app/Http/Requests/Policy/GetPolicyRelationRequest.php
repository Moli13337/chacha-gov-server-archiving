<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Policy;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\Password;
use Illuminate\Validation\Rule;

class GetPolicyRelationRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            // 发布状态
            'obj_type' => ['required', 'integer', Rule::in(array_values(OBJ_TYPE))],
        ];
    }
}