<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 11:45
 */

namespace App\Http\Requests\ApplyCorrect;


use App\Http\Requests\BaseFormRequest;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'string'],
        ];
    }
}