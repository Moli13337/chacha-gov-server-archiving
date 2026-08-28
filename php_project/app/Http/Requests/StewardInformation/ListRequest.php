<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 10:02
 */

namespace App\Http\Requests\StewardInformation;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'publish_status' => ['nullable', 'integer',Rule::in(array_values(PUBLISH_STATUS))],
            'type' => ['nullable', 'integer',Rule::in(array_values(STEWARD_INFORMATION_TYPE))],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}