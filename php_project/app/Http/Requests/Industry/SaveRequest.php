<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Industry;


use App\Http\Requests\BaseFormRequest;
use App\Models\IndustryModel;

class SaveRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'unique:'.IndustryModel::TABLE_NAME],
            'code' => ['required', 'string','unique:'.IndustryModel::TABLE_NAME],
            'parent_id' => ['required', 'integer'],
        ];
    }
}