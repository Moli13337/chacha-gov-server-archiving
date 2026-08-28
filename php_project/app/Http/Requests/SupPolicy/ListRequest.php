<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\SupPolicy;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],

            'start_time' => ['nullable', 'integer'],
            'end_time' => ['nullable', 'integer'],

            'province_code' => ['nullable', 'integer'],
            'city_code' => ['nullable', 'integer'],
            'district_code' => ['nullable', 'integer'],

            // 发文体系
            'gov_agen_first' => ['nullable', 'integer'],
            'gov_agen_second' => ['nullable', 'integer'],

            // 有效期
            'expired' => ['nullable', 'integer'],

            // 收录时间
            'sort' => ['nullable', 'integer'],

            // 发文时间
            'sort_pub' => ['nullable', 'integer'],

            // 发布状态
            'publish_status' => ['nullable', 'integer', Rule::in(array_values(PUBLISH_STATUS))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}