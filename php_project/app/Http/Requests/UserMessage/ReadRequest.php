<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/28
 * Time: 15:27
 */

namespace App\Http\Requests\UserMessage;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserMessageModel;
use Illuminate\Validation\Rule;

class ReadRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(UserMessageModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
        ];
    }
}