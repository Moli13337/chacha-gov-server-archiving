<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Credit;


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
            'department_id' => ['nullable', 'integer'],
            'start_time' => ['nullable', 'integer'],
            'end_time' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}