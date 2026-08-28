<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCalculateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
            'is_calculate' => ['required', 'integer', Rule::in(array_values(IS_CALCULATE))],
        ];
    }
}