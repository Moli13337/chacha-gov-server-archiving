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

class UpdatePublishRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS))],
        ];
    }
}