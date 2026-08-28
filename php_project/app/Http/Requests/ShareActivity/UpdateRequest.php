<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 15:41
 */

namespace App\Http\Requests\ShareActivity;


use App\Http\Requests\BaseFormRequest;
use App\Models\Share\ShareActivityModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(ShareActivityModel::TABLE_NAME)->whereNull('deleted_at')],
            'title' => ['required', 'string', 'max:50', Rule::unique(ShareActivityModel::TABLE_NAME)->ignore($this->input('id'))->whereNull('deleted_at')],
            'content' => ['required', 'string'],
            'number' => ['required', 'integer'],
            'province_code' => ['required', 'integer'],
            'city_code' => ['required', 'integer'],
            'district_code' => ['required', 'integer'],
            'address' => ['required', 'string', 'max:100'],
            'sponsor' => ['required', 'string', 'max:50'],
            'mobile' => ['required', 'string', 'max:20'],
            'type' => ['required', 'integer', Rule::in(array_values(SHARE_ACTIVITY_TYPE))],
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS))],
            'validity_sdate' => ['required', 'integer'],
            'validity_edate' => ['required', 'integer'],
            'file_name' => ['required', 'string', 'max:255'],
            'file_url' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return trans('column.share_activity');
    }
}