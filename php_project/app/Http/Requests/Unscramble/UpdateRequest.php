<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Unscramble;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Models\PolicyUnscrambleModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(PolicyUnscrambleModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required', 'string', Rule::unique(PolicyUnscrambleModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'source_name' => ['nullable', 'string'],

            // 附件
            'content_url' => ['required', 'url'],
            'content_name' => ['required', 'string'],

            // 关联政策
            'policy' => ['required', 'array'],
            'policy.*.policy_id' =>  ['required', 'integer'],
            'policy.*.obj_type' =>  ['required', 'integer'],

            // 发布状态
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS))],

        ];
    }

    public function messages()
    {
        return [
            'name.unique' => trans('validation.custom.name.policy_unique'),
        ];
    }
}