<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 17:01
 */

namespace App\Http\Requests\StewardPush;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class RecordListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'steward_push_id' => ['required', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_publish');
    }
}