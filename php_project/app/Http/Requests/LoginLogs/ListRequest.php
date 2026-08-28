<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\LoginLogs;


use App\Http\Requests\BaseFormRequest;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}