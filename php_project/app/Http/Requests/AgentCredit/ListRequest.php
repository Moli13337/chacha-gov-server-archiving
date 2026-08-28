<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentCredit;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' =>  ['nullable', 'string'],
            'agent_id' =>  ['nullable', 'integer'],
            'is_audit' =>  ['nullable', 'integer', Rule::in(array_values(IS_AUDIT))],
            'type' =>  ['nullable', 'integer', Rule::in(array_values(AGENT_CREDIT_TYPE))],
        ];
    }

    public function attributes()
    {
        return [
            'type' => trans('validation.custom.agent.agent_credit_type')
        ];
    }
}