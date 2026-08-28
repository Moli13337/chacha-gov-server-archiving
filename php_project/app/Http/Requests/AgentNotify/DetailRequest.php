<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 17:31
 */

namespace App\Http\Requests\AgentNotify;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentNotifyModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required','integer', Rule::exists(AgentNotifyModel::TABLE_NAME)->whereNull('deleted_at')],
        ];
    }
}