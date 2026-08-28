<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\MacroPolicy;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Rules\MacroPolicyContent;
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

            // 正文字符串
//            'content_url' => ['nullable', 'string'],
//            'content_name' => ['required_with:content_url', 'string'],
            'content' => ['required_without:content_url', 'string',
                new MacroPolicyContent($this->input('content_url', ''))
            ],


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
            'summarize.*.name' => ['required', 'string','max:30'],
            'summarize.*.summarize' => ['required', 'array', new UniqueTitleSummarize()],
            'summarize.*.summarize.*.title' => ['required', 'string','max:100'],
            'summarize.*.summarize.*.content' => ['required', 'string', 'max:150'],


            // 附件
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],

            // 关联政策 扶持政策
            'sup_policy_relation' => ['nullable', 'array'],
            'sup_policy_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'sup_policy_relation.*.type' =>  ['required', 'integer'],

            // 关联政策 申报通知
            'announce_relation' => ['nullable', 'array'],
            'announce_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'announce_relation.*.type' =>  ['required', 'integer'],

            // 关联政策 相关宏观政策
            'macro_policy_relation' => ['nullable', 'array'],
            'macro_policy_relation.*.obj_type_relation_id' =>  ['required', 'integer'],
            'macro_policy_relation.*.type' =>  ['required', 'integer'],

            // 发布状态
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS)),
                new MacroPolicyPublish($this->input('content_url', ''))],

        ];
    }

    public function messages()
    {
        return [
            'name.unique' => trans('validation.custom.name.policy_unique'),
        ];
    }
}