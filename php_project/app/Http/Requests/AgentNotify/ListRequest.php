<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/18
 * Time: 17:31
 */

namespace App\Http\Requests\AgentNotify;


use App\Http\Requests\BaseFormRequest;
use App\Models\AgentNotifyModel;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'publish_status' => ['nullable', 'integer',Rule::in(array_values(PUBLISH_STATUS))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}