<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 11:45
 */

namespace App\Http\Requests\ApplyCorrect;


use App\Http\Requests\BaseFormRequest;

class InvalidRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'string'],
            'mark' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes()
    {
        return trans('column.apply_correct_invalid');
    }
}