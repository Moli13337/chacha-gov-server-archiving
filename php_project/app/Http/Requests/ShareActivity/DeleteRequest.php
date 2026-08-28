<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 15:41
 */

namespace App\Http\Requests\ShareActivity;


use App\Http\Requests\BaseFormRequest;
use App\Models\Share\ShareActivityModel;
use Illuminate\Validation\Rule;

class DeleteRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('column.share_activity');
    }
}