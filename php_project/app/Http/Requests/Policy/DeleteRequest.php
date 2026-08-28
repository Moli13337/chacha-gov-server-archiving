<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Policy;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Models\UserModel;
use App\Rules\Mobile;
use App\Rules\Password;
use Illuminate\Validation\Rule;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',Rule::exists(PolicyModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
        ];
    }
}