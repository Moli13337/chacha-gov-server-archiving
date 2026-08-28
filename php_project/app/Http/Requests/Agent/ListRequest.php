<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 19:03
 */

namespace App\Http\Requests\Agent;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'type_id' => ['nullable', 'integer'],
            'enterprise_id' => ['nullable', 'integer'],
            'credit_type' => ['nullable', 'integer', Rule::in(array_values(AGENT_CREDIT_TYPE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('validation.custom.agent');
    }
}