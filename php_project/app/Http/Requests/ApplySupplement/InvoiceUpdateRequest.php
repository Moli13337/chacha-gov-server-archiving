<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 15:17
 */

namespace App\Http\Requests\ApplySupplement;


use App\Http\Requests\BaseFormRequest;
use App\Models\ApplyFileModel;
use Illuminate\Validation\Rule;

class InvoiceUpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'apply_id' => ['required', 'integer'],
            'file_id' => ['nullable', 'integer',
                Rule::exists(ApplyFileModel::TABLE_NAME, 'id')
                    ->where('apply_id', $this->input('apply_id'))
                    ->whereNull('deleted_at')
            ],
            'invoice_number' => ['required', 'numeric'],
            'invoice_code' => ['required', 'numeric'],
            'invoice_billing_date' => ['nullable', 'integer'],
            'invoice_money' => ['nullable', 'numeric'],
        ];
    }
}