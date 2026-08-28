<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/3
 * Time: 16:10
 */

namespace App\Http\Requests\Workbench;


use App\Http\Requests\BaseFormRequest;

class ApplyEnterpriseProjectRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'start_time' => ['required', 'integer'],
            'end_time' => ['required', 'integer'],
            'project_id' => ['required', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}