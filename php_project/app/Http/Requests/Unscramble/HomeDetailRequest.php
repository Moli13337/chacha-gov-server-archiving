<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Unscramble;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyUnscrambleModel;
use Illuminate\Validation\Rule;

class HomeDetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'string'],
        ];
    }
}