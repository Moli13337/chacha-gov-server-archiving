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

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required','integer', Rule::exists(AgentNotifyModel::TABLE_NAME)->whereNull('deleted_at')],
            'title' => ['required','string','max:50', Rule::unique(AgentNotifyModel::TABLE_NAME)->ignore($this->input('id'))->whereNull('deleted_at')],
            'content' => ['required','string'],
            'source_name' => ['required','string','max:50'],
            'publish_status' => ['required','integer',Rule::in(array_values(PUBLISH_STATUS))],
            // 附件
            'file' => ['nullable', 'array'],
            'file.*.id' => ['nullable', 'integer'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
        ];

    }

    public function attributes()
    {
        return trans('column.agent_notify');
    }
}