<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/9
 * Time: 16:30
 */

namespace App\Http\Requests\AgentEnterprise;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}