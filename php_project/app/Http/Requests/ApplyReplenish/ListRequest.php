<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:56
 */

namespace App\Http\Requests\ApplyReplenish;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', Rule::in([0,1])],
            'start_time' => ['nullable', 'integer'],
            'end_time' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}