<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 10:23
 */

namespace App\Http\Requests\UserCollection;


use App\Http\Requests\BaseFormRequest;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'obj_enc_id' => ['required', 'string'],
            'obj_type' => ['required', 'integer'],
        ];
    }

}