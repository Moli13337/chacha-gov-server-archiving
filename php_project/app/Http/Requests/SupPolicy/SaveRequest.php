<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\SupPolicy;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Rules\MacroPolicyPublish;
use App\Rules\UniqueDirection;
use App\Rules\UniqueGovAgen;
use App\Rules\UniqueTitleSummarize;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique(PolicyModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'doc_num' => ['nullable', 'string', Rule::unique(PolicyModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'pub_time' => ['nullable', 'integer'],
            'province_code' => ['nullable', 'integer'],
            'city_code' => ['nullable', 'integer'],
            'district_code' => ['nullable', 'integer'],
            // 发文体系
            'gov_agen' => ['nullable', 'array', new UniqueGovAgen()],
            'gov_agen.*.gov_agen_first' => ['required', 'integer'],
            'gov_agen.*.gov_agen_second' => ['required', 'integer'],

            // 有效期
            'validity_sdate' => ['required_with:validity_edate', 'integer'],
            'validity_edate' => ['required_with:validity_sdate', 'integer'],

            'content' => ['required', 'string'],

            // 行业
            "industry" => ['nullable', 'array'],
            "industry.*.first_industry_id" => ['required_with:industry.*.second_industry_id', 'integer'],
            "industry.*.second_industry_id" => ['required_with:industry.*.third_industry_id', 'integer'],
            "industry.*.third_industry_id" => ['required_with:industry.*.fourth_industry_id', 'integer'],
            "industry.*.fourth_industry_id" => ['nullable', 'integer'],

            'source' => ['nullable', 'string'],
            'source_web' => ['nullable', 'string'],
            'source_url' => ['nullable', 'string'],

            // 政策概述
            'summarize' => ['nullable', 'array', new UniqueDirection()],
            'summarize.*.name' => ['required', 'string', 'max:30'],
            'summarize.*.summarize' => ['required', 'array', new UniqueTitleSummarize()],
            'summarize.*.summarize.*.title' => ['required', 'string', 'max:100'],
            'summarize.*.summarize.*.content' => ['required', 'string', 'max:150'],

            // 附件
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],


            // 条款
            'item' => ['required', 'array'],
            'item.*.content' => ['required', 'string'],

            // 结束语
            'conclusion.conclusion' => ['required', 'string'],

            // 关联政策 宏观政策
            'macro_policy_relation' => ['nullable', 'array'],
            'macro_policy_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'macro_policy_relation.*.type' =>  ['required', 'integer'],

            // 关联政策 实施细则
            'imple_regu_relation' => ['nullable', 'array'],
            'imple_regu_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'imple_regu_relation.*.type' =>  ['required', 'integer'],

            // 关联政策 申报通知
            'announce_relation' => ['nullable', 'array'],
            'announce_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'announce_relation.*.type' =>  ['required', 'integer'],

            // 关联政策 相关扶持政策
            'publicity_relation' => ['nullable', 'array'],
            'publicity_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'publicity_relation.*.type' =>  ['required', 'integer'],


            // 发布状态
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS)),
                new MacroPolicyPublish($this->input('content_url', ''))],

        ];
    }
}