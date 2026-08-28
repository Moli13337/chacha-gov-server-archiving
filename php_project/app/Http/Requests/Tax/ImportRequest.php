<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/24
 * Time: 15:07
 */

namespace App\Http\Requests\Tax;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ImportRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'file_url' => ['required', 'string'],
            'year' => ['required', 'date_format:Y'],
            'type' => ['required', 'integer', Rule::in(array_values(TAX_TYPE))],
        ];
    }
}