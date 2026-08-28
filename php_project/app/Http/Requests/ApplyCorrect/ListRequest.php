<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 11:45
 */

namespace App\Http\Requests\ApplyCorrect;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', Rule::in(APPLY_CORRECT_STATUS)],
            'start_time' => ['nullable', 'integer'],
            'end_time' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}