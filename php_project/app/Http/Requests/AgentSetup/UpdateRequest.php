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

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required','integer', Rule::exists(AgentSetupModel::TABLE_NAME)->whereNull('deleted_at')],
            'title' => ['required','string','max:50'],
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
}