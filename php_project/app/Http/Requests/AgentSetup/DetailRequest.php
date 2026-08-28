<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:27
 */

namespace App\Http\Requests\AgentSetup;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'type' => ['required','integer',Rule::in(array_values(AGENT_SETUP_TYPE))],
        ];
    }
}