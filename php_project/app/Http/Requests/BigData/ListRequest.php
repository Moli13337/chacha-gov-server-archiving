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

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer'],
            // 发文时间
            'sort_pub' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'id' => ['nullable', 'integer'],
            'next' => ['nullable', 'integer', Rule::in(array_values(NEXT_PAGE))],
        ];
    }
}