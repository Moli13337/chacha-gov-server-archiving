<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/6
 * Time: 17:42
 */

namespace App\Http\Requests\StewardPush;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class TrumpetRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'obj_type' => ['nullable', 'integer', Rule::in(array_values(STEWARD_PUSH_OBJ_TYPE))],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
        ];
    }
}