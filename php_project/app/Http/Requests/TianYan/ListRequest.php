<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\TianYan;


use App\Http\Requests\BaseFormRequest;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}