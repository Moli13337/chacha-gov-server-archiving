<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentCredit;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentCreditModel;
use Illuminate\Validation\Rule;

class UpdateShowRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(AgentCreditModel::TABLE_NAME)->whereNull('deleted_at')],
            'is_show' => ['required', 'integer', Rule::in(array_values(IS_SHOW))],
        ];
    }
}