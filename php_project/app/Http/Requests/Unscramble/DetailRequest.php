<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Unscramble;


use App\Http\Requests\BaseFormRequest;
use App\Models\PolicyModel;
use App\Models\PolicyUnscrambleModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(PolicyUnscrambleModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],

        ];
    }
}