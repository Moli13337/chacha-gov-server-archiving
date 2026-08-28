<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/2
 * Time: 18:21
 */

namespace App\Http\Requests\Apply;


use App\Http\Requests\BaseFormRequest;
use App\Models\ApplyModel;
use Illuminate\Validation\Rule;

class RefreshPrecheckRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'apply_id' => ['required', 'integer', Rule::exists(ApplyModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
        ];
    }
}