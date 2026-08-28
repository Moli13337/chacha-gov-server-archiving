<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/6
 * Time: 13:56
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use App\Models\StaffModel;
use Illuminate\Validation\Rule;

class StewardRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'staff_id' => ['required', 'integer', Rule::exists(StaffModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_user');
    }

    public function messages()
    {
        return [
            'staff_id.required' => trans('column.steward_user.staff_id'),
            'staff_id.integer' => trans('column.steward_user.staff_id'),
            'staff_id.exists' => trans('column.steward_user.staff_id'),
            'user_ids.required' => trans('column.steward_user.user_ids'),
            'user_ids.array' => trans('column.steward_user.user_ids'),
            'user_ids.*.required' => trans('column.steward_user.user_ids'),
            'user_ids.*.integer' => trans('column.steward_user.user_ids'),
            'user_ids.*.min' => trans('column.steward_user.user_ids'),
        ];
    }
}