<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComplaint\Home;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentModel;
use App\Rules\Captcha;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'agent_id' => ['required', 'integer', Rule::exists(AgentModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'content' => ['required', 'string', 'max:300'],
            'key' => ['required', 'string'],
            'captcha' => ['required', new Captcha($this->input('key'))]
        ];
    }
}