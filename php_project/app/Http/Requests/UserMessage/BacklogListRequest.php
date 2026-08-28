<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 9:43
 */

namespace App\Http\Requests\UserMessage;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class BacklogListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'is_read' => ['nullable', 'integer', Rule::in(array_values(USER_MESSAGE_READ))],
            'source_type_id' => ['nullable', 'integer', Rule::in(array_values(USER_MESSAGE_SOURCE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}