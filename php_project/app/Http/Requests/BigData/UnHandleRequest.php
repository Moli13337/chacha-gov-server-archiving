<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\BigData;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UnHandleRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'obj_type' => ['required', 'integer',Rule::In(array_values(OBJ_TYPE))],
            'keyword' => ['nullable', 'string'],
            'start_time' => ['nullable', 'integer'],
            'end_time' => ['nullable', 'integer'],
            'sort' => ['nullable', 'integer'],
            // 发文时间
            'sort_pub' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'id' => ['nullable', 'integer'],
            'next' => ['nullable', 'integer', Rule::in(array_values([1,2]))],
        ];
    }
}