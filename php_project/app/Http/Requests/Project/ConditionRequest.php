<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:05
 */

namespace App\Http\Requests\Project;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ConditionRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable','string'],
            'per_page' => ['nullable', 'integer'],
        ];
    }
}
