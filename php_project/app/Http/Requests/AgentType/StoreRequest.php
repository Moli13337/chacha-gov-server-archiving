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

class StoreRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:10', Rule::unique(AgentTypeModel::TABLE_NAME)
                ->where('parent_id', 0)->whereNull('deleted_at')],
        ];
    }
}