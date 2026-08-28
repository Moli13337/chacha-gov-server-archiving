<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 17:01
 */

namespace App\Http\Requests\StewardPush;


use App\Http\Requests\BaseFormRequest;
use App\Models\Steward\StewardPushModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(StewardPushModel::TABLE_NAME)->whereNull('deleted_at')],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_publish');
    }
}