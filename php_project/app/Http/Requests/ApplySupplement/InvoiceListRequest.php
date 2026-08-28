<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 15:17
 */

namespace App\Http\Requests\ApplySupplement;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class InvoiceListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'ocr' => ['nullable', 'integer', Rule::in(array_values(APPLY_EXCEPTION_OCR))],
            'repeat' => ['nullable', 'integer', Rule::in(array_values(APPLY_EXCEPTION_REPEAT))],
            'apply_id' => ['required', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}