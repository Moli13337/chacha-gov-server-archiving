<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:05
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;

class SaveEnterpriseRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required','string', 'max:50'],
            'unified_credit_code' => ['required','string', 'max:20'],
            'legal_represent' => ['required','string', 'max:50'],
            'business_license_url' => ['required','string', 'max:200'],
        ];
    }
}