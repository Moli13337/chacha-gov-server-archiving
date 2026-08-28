<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComplaint;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' =>  ['nullable', 'string'],
            'status' =>  ['nullable', 'integer', Rule::in(array_values(AGENT_COMPLAINT_STATUS))],
            'type' =>  ['nullable', 'integer', Rule::in(array_values(AGENT_COMPLAINT_TYPE))],
            'agent_id' =>  ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('validation.custom.agentComplaint');
    }
}