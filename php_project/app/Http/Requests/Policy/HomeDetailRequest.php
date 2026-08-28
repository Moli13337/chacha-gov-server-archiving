<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 10:22
 */

namespace App\Http\Requests\Policy;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use Illuminate\Validation\Rule;

class HomeDetailRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'string', Rule::exists(PolicyModel::TABLE_NAME, 'enc_id')],
        ];
    }
}