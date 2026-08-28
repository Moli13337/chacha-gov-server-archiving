<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/3
 * Time: 15:37
 */

namespace App\Http\Requests\UserFeedback;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'type' => ['nullable', 'integer', Rule::in(array_values(FEEDBACK_TYPE))],
            'keyword' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', Rule::in(array_values(FEEDBACK_STATUS))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}