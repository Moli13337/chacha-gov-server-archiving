<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Information;


use App\Http\Requests\BaseFormRequest;
use App\Models\InformationModel;
use Illuminate\Validation\Rule;

class UpdateInformationRequest extends BaseFormRequest
{
    public function rules()
    {
        $table_name = InformationModel::TABLE_NAME;
        return [
            'id' => ['required', 'integer', Rule::exists($table_name)],
            'title' => ['required', 'string', Rule::unique($table_name)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'content' => ['required', 'string'],
            'source_name' => ['required', 'string'],
            'order_num' => ['nullable', 'integer'],
        ];
    }
}