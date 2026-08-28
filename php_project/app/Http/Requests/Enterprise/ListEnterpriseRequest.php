<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:05
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListEnterpriseRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable','string'],
            'relation_status' => ['nullable', 'integer', Rule::in(USER_ENTERPRISE_RELATION_STATUS)],
            "first_industry_id" => ['nullable', 'integer'],
            "second_industry_id" => ['nullable', 'integer'],
            "third_industry_id" => ['nullable', 'integer'],
            "fourth_industry_id" => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}