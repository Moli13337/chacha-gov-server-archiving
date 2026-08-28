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

class DetailInformationRequest extends BaseFormRequest
{
    public function rules()
    {
        $table_name = InformationModel::TABLE_NAME;
        return [
            'id' => ['required', 'integer', Rule::exists($table_name)],
        ];
    }
}