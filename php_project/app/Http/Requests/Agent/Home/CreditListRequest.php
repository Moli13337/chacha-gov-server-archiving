<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 19:03
 */

namespace App\Http\Requests\Agent\Home;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreditListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'credit_type' => ['required', 'integer', Rule::in(array_values(AGENT_CREDIT_TYPE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}