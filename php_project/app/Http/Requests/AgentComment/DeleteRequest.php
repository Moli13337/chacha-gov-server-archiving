<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment;


use App\Http\Requests\BaseFormRequest;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer']
        ];
    }
}