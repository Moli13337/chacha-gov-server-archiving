<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:32
 */

namespace App\Http\Requests\ApprovalCoordinate;


use App\Http\Requests\BaseFormRequest;

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'log_id' => ['required', 'integer'],
        ];
    }
}