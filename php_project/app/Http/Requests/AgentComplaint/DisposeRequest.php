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

class DisposeRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
            'content' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'integer', Rule::in(array_values(AGENT_COMPLAINT_TYPE))],
        ];
    }

    public function attributes()
    {
        return [
            'type' => trans('validation.custom.agentComplaint.type'),
            'content' => trans('validation.custom.agentComplaint.remark'),
        ];
    }
}