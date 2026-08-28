<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/3
 * Time: 16:10
 */

namespace App\Http\Requests\Workbench;


use App\Http\Requests\BaseFormRequest;

class ApplyOverviewRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'start_time' => ['required', 'integer'],
            'end_time' => ['required', 'integer'],
            'mold_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}