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
use Illuminate\Validation\Rule;

class UpdatePublishRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(AgentModel::TABLE_NAME)->whereNull('deleted_at')],
            'publish_status' => ['required','integer',Rule::in(array_values(PUBLISH_STATUS))],
        ];
    }
}