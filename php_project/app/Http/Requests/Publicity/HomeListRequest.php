<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 18:07
 */

namespace App\Http\Requests\Publicity;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class HomeListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'province_code' => ['nullable', 'integer'],
            'city_code' => ['nullable', 'integer'],
            'district_code' => ['nullable', 'integer'],
            'keyword' => ['nullable', 'string'],
            'obj_type' => ['nullable', 'integer', Rule::in([OBJ_TYPE['announce'],OBJ_TYPE['publicity'],OBJ_TYPE['approval']])],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}