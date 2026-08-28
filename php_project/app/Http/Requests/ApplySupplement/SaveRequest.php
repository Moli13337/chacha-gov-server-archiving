<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 15:17
 */

namespace App\Http\Requests\ApplySupplement;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use App\Models\ProjectModel;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'apply_id' => ['required', 'integer'],
            'invoice' => ['nullable', 'array'],
            'invoice.*.file_name' => ['required', 'string'],
            'invoice.*.file_url' => ['required', 'string'],
            'invoice.*.id' => ['nullable', 'integer'],
        ];
    }
}