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

class StoreChildrenRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'parent_id' => ['required', 'integer',  Rule::exists(AgentTypeModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:10', Rule::unique(AgentTypeModel::TABLE_NAME)
                ->where('parent_id', $this->input('parent_id'))->whereNull('deleted_at')],
        ];
    }
}