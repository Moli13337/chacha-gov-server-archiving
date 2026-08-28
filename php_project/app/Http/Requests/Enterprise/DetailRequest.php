<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',Rule::exists(EnterpriseModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
        ];
    }
}