<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 19:03
 */

namespace App\Http\Requests\Agent\Home;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'string', Rule::exists(AgentModel::TABLE_NAME, 'enc_id')->whereNull('deleted_at')],
        ];
    }
}