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

class StoreRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'enterprise_id' => ['required', 'integer', Rule::exists(EnterpriseModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'project_id' => ['required', 'integer', Rule::exists(ProjectModel::TABLE_NAME, 'id')->whereNull('deleted_at')],
            'apply_money' => ['required', 'numeric'],
            'support_content' => ['required', 'numeric'],
            'allocation_time' => ['required', 'numeric'],
            'submit_time' => ['required', 'numeric'],
        ];
    }
}