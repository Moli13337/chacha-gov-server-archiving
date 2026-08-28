<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 17:01
 */

namespace App\Http\Requests\StewardPush;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'obj_id' => ['required', 'integer'],
            'obj_enc_id' => ['required', 'string','max:30'],
            'obj_type' => ['required', 'integer', Rule::in(array_values(STEWARD_PUSH_OBJ_TYPE))],
            'obj_title' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', Rule::in(array_values(STEWARD_PUSH_TYPE))],
            "first_industry_id" => ['required_if:type,1', 'integer'],
            "second_industry_id" => ['nullable', 'integer'],
            "third_industry_id" => ['nullable', 'integer'],
            "fourth_industry_id" => ['nullable', 'integer'],
            'touser' => ['required_if:type,1', 'array'],
            'touser.*.user_id' => ['required', 'integer'],
            'touser.*.enterprise_id' => ['required', 'integer'],
            'touser.*.mobile' => ['required', 'string', 'max:20'],
            'touser.*.enterprise_name' => ['required', 'string', 'max:255'],
            'touser.*.user_name' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_publish');
    }

    public function messages()
    {
        return [
            'touser.required_if' => trans('validation.custom.steward_push.touser.required_if'),
            'first_industry_id.required_if' => trans('validation.custom.steward_push.first_industry_id'),
            'first_industry_id.integer' => trans('validation.custom.steward_push.first_industry_id'),
            'second_industry_id.integer' => trans('validation.custom.steward_push.second_industry_id'),
            'third_industry_id.integer' => trans('validation.custom.steward_push.third_industry_id'),
            'fourth_industry_id.integer' => trans('validation.custom.steward_push.fourth_industry_id'),
        ];
    }
}