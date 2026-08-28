<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 18:21
 */

namespace App\Http\Requests\ApplyCorrect;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class AgreeRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'string'],
        ];
    }
}