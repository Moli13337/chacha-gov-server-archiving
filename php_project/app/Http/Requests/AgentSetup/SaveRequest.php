<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:27
 */

namespace App\Http\Requests\AgentSetup;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentSetupModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => ['required','string','max:50'],
            'content' => ['required','string'],
            'source_name' => ['required','string','max:50'],
            'type' => ['required','int',Rule::in(array_values(AGENT_SETUP_TYPE)),
                Rule::unique(AgentSetupModel::TABLE_NAME)->whereNull('deleted_at')],
            'publish_status' => ['required','integer',Rule::in(array_values(PUBLISH_STATUS))],
            // 附件
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
        ];
    }
}