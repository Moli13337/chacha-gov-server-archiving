<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 17:31
 */

namespace App\Http\Requests\AgentNotify\Home;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentNotifyModel;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}