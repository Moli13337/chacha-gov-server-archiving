<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:50
 */

namespace App\Http\Requests\Project;


use App\Http\Requests\BaseFormRequest;
use App\Models\ProjectModel;
use Illuminate\Validation\Rule;

class UpdatePublishRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer',Rule::exists(ProjectModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            // 发布状态
            'publish_status' => ['required', 'integer', Rule::in(array_values(PUBLISH_STATUS))],

        ];
    }
}