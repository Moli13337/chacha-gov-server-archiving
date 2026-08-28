<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Unscramble;


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

            // 收录时间
            'sort' => ['nullable', 'integer'],

            // 发布状态
            'publish_status' => ['nullable', 'integer', Rule::in(array_values(PUBLISH_STATUS))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}