<?php
/**
 * Created by PhpStorm.
 * Date: 2019/6/4
 * Time: 17:46
 */

namespace App\Http\Requests;


use App\Common\Code;
use App\Exceptions\ValidatorException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidatorException(Code::PARAM_ERROR, $validator->errors()->first());
    }

    abstract public function rules();
}