<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:32
 */

namespace App\Http\Requests\ApprovalCoordinate;


use App\Http\Requests\BaseFormRequest;

class LogRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'apply_id' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
        ];
    }
}