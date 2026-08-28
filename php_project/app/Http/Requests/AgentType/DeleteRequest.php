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

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',  Rule::exists(AgentTypeModel::TABLE_NAME)->whereNull('deleted_at')],
        ];
    }
}