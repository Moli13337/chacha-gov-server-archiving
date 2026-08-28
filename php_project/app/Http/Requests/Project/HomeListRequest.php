<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/26
 * Time: 11:38
 */

namespace App\Http\Requests\Project;


use App\Http\Requests\BaseFormRequest;

class HomeListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'announce_status' => ['nullable', 'integer'],
            'province_code' => ['nullable', 'integer'],
            'city_code' => ['nullable', 'integer'],
            'district_code' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'mold_id' => ['nullable', 'integer'],
            'steward_push' => ['nullable', 'integer'],
        ];
    }
}