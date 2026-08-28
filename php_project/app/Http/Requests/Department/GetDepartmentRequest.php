<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 17:20
 */

namespace App\Http\Requests\Department;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class GetDepartmentRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'type' => ['required', 'integer', Rule::in(array_values(DEPARTMENT_TYPE)) ]
        ];
    }
}