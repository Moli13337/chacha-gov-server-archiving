<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentCredit;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'agent_id' => ['required', 'integer',Rule::exists(AgentModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'type' => ['required', 'integer', Rule::in(array_values(AGENT_CREDIT_TYPE))],
            'project_name' => ['required', 'string', 'max:30'],
            'content' => ['required', 'string', 'max:500'],
            'province_code' => ['required', 'integer'],
            'city_code' => ['required', 'integer'],
            'district_code' => ['required', 'integer'],
        ];
    }

    public function attributes()
    {
        return [
            'type' => trans('validation.custom.agent.agent_credit_type'),
            'content' => trans('validation.custom.agent.agent_credit_content'),
        ];
    }
}