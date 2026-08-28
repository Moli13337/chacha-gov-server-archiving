<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 17:25
 */

namespace App\Http\Requests\Staff;


use App\Http\Controllers\Controller;
use App\Http\Requests\BaseFormRequest;
use App\Models\StaffModel;
use Illuminate\Validation\Rule;

class SendSmsRoleRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer']
        ];
    }
}