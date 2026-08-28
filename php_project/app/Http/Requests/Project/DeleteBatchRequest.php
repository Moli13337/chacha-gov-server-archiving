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

class DeleteBatchRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ];
    }
}