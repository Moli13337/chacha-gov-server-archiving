<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:56
 */

namespace App\Http\Requests\ApplyReplenish;


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