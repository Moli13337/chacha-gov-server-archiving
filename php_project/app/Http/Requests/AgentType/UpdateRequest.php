<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/10
 * Time: 15:15
 */

namespace App\Http\Requests\AgentType;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentTypeModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',  Rule::exists(AgentTypeModel::TABLE_NAME)->whereNull('deleted_at')],
//            'parent_id' => ['required', 'integer',  Rule::exists(AgentTypeModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'parent_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:10', Rule::unique(AgentTypeModel::TABLE_NAME)->ignore($this->input('id'))
                ->where('parent_id', $this->input('parent_id'))->whereNull('deleted_at')],
        ];
    }
}