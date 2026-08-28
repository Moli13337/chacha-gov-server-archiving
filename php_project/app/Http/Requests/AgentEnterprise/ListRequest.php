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

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'type_id' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}