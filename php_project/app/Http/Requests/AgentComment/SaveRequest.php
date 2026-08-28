<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'agent_id' => ['required', 'integer',Rule::exists(AgentModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'stars' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:300'],
        ];
    }
}