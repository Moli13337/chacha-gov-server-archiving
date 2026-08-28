<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 15:17
 */

namespace App\Http\Requests\ApplySupplement;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use App\Models\ProjectModel;
use Illuminate\Validation\Rule;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}