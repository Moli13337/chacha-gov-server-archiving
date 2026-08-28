<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\UserEnterpriseRelation;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\Password;
use App\Rules\SmsCode;
use Illuminate\Validation\Rule;

class UpdateRelationRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists(UserModel::TABLE_NAME, 'id')->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'enterprise_id' => ['required', 'integer', Rule::exists(EnterpriseModel::TABLE_NAME, 'id')->where(function ($query){
                $query->whereNull('deleted_at');
            })],
        ];
    }

    public function attributes()
    {
        return trans('column.enterprise_user_relation');
    }

    public function messages()
    {
        return [
            'user_id.required' => trans('column.enterprise_user_relation.user_id'),
            'user_id.integer' => trans('column.enterprise_user_relation.user_id'),
            'user_id.exists' => trans('column.enterprise_user_relation.user_id'),
            'enterprise_id.required' => trans('column.enterprise_user_relation.enterprise_id'),
            'enterprise_id.integer' => trans('column.enterprise_user_relation.enterprise_id'),
            'enterprise_id.exists' => trans('column.enterprise_user_relation.enterprise_id'),
        ];
    }
}