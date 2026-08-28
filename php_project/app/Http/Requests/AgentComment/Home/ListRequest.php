<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment\Home;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'stars' =>  ['nullable', 'integer', 'between:1,5'],
            'agent_id' =>  ['required', 'integer'],
            'user_type' =>  ['required', 'integer', Rule::in(array_values(MESSAGE_USER_TYPE))],
        ];
    }
}