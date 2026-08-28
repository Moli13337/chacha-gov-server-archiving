<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 11:29
 */

namespace App\Http\Requests\Approval;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class CoordinateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'approval_id' => ['required', 'integer'],
            'department_list' => ['required', 'array'],
            'department_list.*.department_id' => ['required', 'integer'],
            'department_list.*.remark' => ['nullable', 'string', 'max:500'],
            'start_time' => ['required', 'integer'],
            'end_time' => ['required', 'integer']
        ];
    }
}