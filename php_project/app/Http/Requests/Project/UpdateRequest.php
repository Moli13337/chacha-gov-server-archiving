<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Project;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Models\ProjectModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',Rule::exists(ProjectModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'policy_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', Rule::unique(ProjectModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'policy_basis' => ['required', 'string'],
            'sup_object' => ['required', 'string'],
            'sup_content' => ['required', 'string'],
            'apply_condition' => ['required', 'string'],
            'policy_advisory' => ['required', 'string'],

            'materials' => ['required', 'array'],
            'materials.*.name' => ['required', 'string'],
            'materials.*.is_need' => ['required', 'integer', Rule::in(array_values(MATERIALS_NEED))],
            'materials.*.type' => ['required','integer', Rule::in(array_values(MATERIALS_TYPE))],

            'materials_other' => ['nullable', 'array'],
            'materials_other.content' => ['required', 'string'],

            // 政策类型
            'mold_id' => ['required', 'integer'],

            // 有效期
            'validity_sdate' => ['required', 'integer'],
            'validity_edate' => ['required', 'integer'],

            // 附件
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],

            // 发布状态
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS))],

            // 行业
            "industry" => ['nullable', 'array'],
            "industry.*.first_industry_id" => ['required_with:industry.*.second_industry_id', 'integer'],
            "industry.*.second_industry_id" => ['required_with:industry.*.third_industry_id', 'integer'],
            "industry.*.third_industry_id" => ['required_with:industry.*.fourth_industry_id', 'integer'],
            "industry.*.fourth_industry_id" => ['nullable', 'integer'],

        ];
    }
}