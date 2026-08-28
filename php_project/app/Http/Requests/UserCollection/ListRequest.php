<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 10:23
 */

namespace App\Http\Requests\UserCollection;


use App\Http\Requests\BaseFormRequest;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'obj_type' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

}