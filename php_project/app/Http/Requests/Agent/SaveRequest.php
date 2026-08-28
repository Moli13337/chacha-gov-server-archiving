<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 19:03
 */

namespace App\Http\Requests\Agent;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentModel;
use App\Models\EnterpriseModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'enterprise_id' => ['required', 'integer', Rule::exists(EnterpriseModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'type_id' => ['required', 'integer', Rule::unique(AgentModel::TABLE_NAME)->where('enterprise_id', $this->input('enterprise_id'))->whereNull('deleted_at')],
            'service_item' => ['required', 'string', 'max:1000'],
            'file_name' => ['required', 'string'],
            'file_url' => ['required', 'string'],
            'service_detail' => ['nullable', 'string'],
            'province_code' => ['required', 'integer'],
            'city_code' => ['required', 'integer'],
            'district_code' => ['required', 'integer'],
            'address' => ['required', 'string', 'max:50'],
            'contact_name' => ['required', 'string', 'max:20'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'remark' => ['nullable', 'string', 'max:500'],
            'submit_time' => ['required', 'integer'],
            'submit_material' => ['required', 'integer', Rule::in(array_values(AGENT_SUBMIT_MATERIAL))],
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return trans('validation.custom.agent');
    }

    public function messages()
    {
        return [
            'type_id.unique' => trans('validation.custom.agent_validation.type_id.exist'),
        ];
    }
}