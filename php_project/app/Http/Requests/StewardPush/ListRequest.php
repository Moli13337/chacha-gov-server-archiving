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

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'obj_type' => ['nullable', 'integer',Rule::in(array_values(STEWARD_PUSH_OBJ_TYPE))],
            'type' => ['nullable', 'integer', Rule::in(array_values(STEWARD_PUSH_TYPE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_publish');
    }
}