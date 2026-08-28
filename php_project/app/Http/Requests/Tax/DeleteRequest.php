<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Tax;


use App\Http\Requests\BaseFormRequest;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ];
    }
}