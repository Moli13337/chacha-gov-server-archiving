<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Publicity;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Rules\UniqueGovAgen;
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

            'source' => ['nullable', 'string'],
            'source_web' => ['nullable', 'string'],
            'source_url' => ['nullable', 'string'],


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