<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 9:43
 */

namespace App\Http\Requests\UserMessage;


use App\Http\Requests\BaseFormRequest;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}