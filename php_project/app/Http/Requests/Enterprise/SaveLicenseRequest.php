<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/9
 * Time: 16:29
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use Illuminate\Validation\Rule;

class SaveLicenseRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(EnterpriseModel::TABLE_NAME)->whereNull('deleted_at')],
            'business_license_url' => ['required', 'string', 'max:255'],
        ];
    }
}