<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 10:02
 */

namespace App\Http\Requests\StewardInformation;


use App\Http\Requests\BaseFormRequest;
use App\Models\Steward\StewardInformationModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(StewardInformationModel::TABLE_NAME)->whereNull('deleted_at')],
            'title' => ['required', 'string','max:50', Rule::unique(StewardInformationModel::TABLE_NAME)->ignore($this->input('id'))->whereNull('deleted_at')],
            'source_name' => ['required', 'string','max:50'],
            'content' => ['required', 'string'],
            'type' => ['required', 'integer', Rule::in(array_values(STEWARD_INFORMATION_TYPE))],
            'link' => ['nullable', 'string', 'max:255'],
            'publish_status' => ['required','integer',Rule::in(array_values(PUBLISH_STATUS))],
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_opinion');
    }
}