<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 17:35
 */

namespace App\Http\Requests\AgentComment;


use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
            'stars' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:300'],
        ];
    }
}