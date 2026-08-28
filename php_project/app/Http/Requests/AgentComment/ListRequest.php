<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' =>  ['nullable', 'string'],
            'stars' =>  ['nullable', 'integer', 'between:1,5'],
            'is_show' =>  ['nullable', 'integer', Rule::in(array_values(IS_SHOW))],
            'user_type' =>  ['nullable', 'integer', Rule::in(array_values(MESSAGE_USER_TYPE))],
            'agent_id' =>  ['nullable', 'integer'],
        ];
    }
}