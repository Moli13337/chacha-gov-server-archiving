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

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'publish_status' => ['nullable', 'integer',Rule::in(array_values(PUBLISH_STATUS))],
            'status' => ['nullable', 'integer',Rule::in(array_values(SHARE_ACTIVITY_STATUS))],
            'type' => ['nullable', 'integer',Rule::in(array_values(SHARE_ACTIVITY_TYPE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('column.share_activity');
    }
}