<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Tax;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',Rule::exists(EnterpriseModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'type' => ['nullable', 'integer', Rule::in(TAX_TYPE)],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}