<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 17:30
 */

namespace App\Http\Requests\AgentNotify;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentNotifyModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => ['required','string','max:50', Rule::unique(AgentNotifyModel::TABLE_NAME)->whereNull('deleted_at')],
            'content' => ['required','string'],
            'source_name' => ['required','string','max:50'],
            'publish_status' => ['required','integer',Rule::in(array_values(PUBLISH_STATUS))],
            // 附件
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return trans('column.agent_notify');
    }
}