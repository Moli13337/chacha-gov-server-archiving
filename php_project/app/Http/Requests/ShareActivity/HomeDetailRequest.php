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

class HomeDetailRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'string',Rule::exists(ShareActivityModel::TABLE_NAME, 'enc_id')
                ->where('publish_status', PUBLISH_STATUS['yes'])
                ->whereNull('deleted_at')],
        ];
    }

    public function attributes()
    {
        return trans('column.share_activity');
    }
}