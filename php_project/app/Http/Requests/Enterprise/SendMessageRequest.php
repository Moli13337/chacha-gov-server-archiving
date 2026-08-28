<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/24
 * Time: 16:23
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;

class SendMessageRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:20'],
            'content' => ['required', '', 'max:500'],
            'enterprise_ids' => ['required', 'array'],
            'enterprise_ids.*' => ['required', 'integer'],
        ];
    }
}