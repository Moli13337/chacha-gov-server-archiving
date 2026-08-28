<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\User;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'relation_status' => ['nullable', 'integer', Rule::in(USER_ENTERPRISE_RELATION_STATUS)],
            'is_forbidden' => ['nullable', 'integer', Rule::in(array_values(USER_FORBIDDEN))],
            'order_type' => ['nullable', 'integer', Rule::in(array_values(USER_LIST_ORDER_TYPE))],
            'page' =>  ['nullable', 'string'],
            'per_page' =>  ['nullable', 'string'],
        ];
    }
}