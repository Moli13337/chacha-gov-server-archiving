<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 13:54
 */

namespace App\Http\Requests\Staff;


use App\Http\Requests\BaseFormRequest;
use App\Models\StaffModel;
use App\Rules\Mobile;
use App\Rules\StaffSmsCode;
use Illuminate\Validation\Rule;

class RegisterRequest extends BaseFormRequest
{

    public function rules()
    {
        $table_name = StaffModel::TABLE_NAME;
        return [
            'name' => ['required', 'string', 'max:20'],
            'mobile' => ['required', 'integer', new Mobile()],
            'code' => ['required', 'string', new StaffSmsCode($this->input('mobile'))],
            'uid' => ['required', 'string', Rule::unique($table_name)->whereNull('deleted_at')],
        ];
    }
}