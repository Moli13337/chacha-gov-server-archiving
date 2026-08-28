<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 10:22
 */

namespace App\Http\Requests\Policy;


use App\Http\Requests\BaseFormRequest;

class HomeListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'province_code' => ['nullable', 'integer'],
            'city_code' => ['nullable', 'integer'],
            'district_code' => ['nullable', 'integer'],
            'industry' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}