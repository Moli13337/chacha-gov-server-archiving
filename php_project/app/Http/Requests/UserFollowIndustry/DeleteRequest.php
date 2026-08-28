<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 18:54
 */

namespace App\Http\Requests\UserFollowIndustry;


use App\Http\Requests\BaseFormRequest;
use App\Models\UserFollowIndustryModel;
use Illuminate\Validation\Rule;

class DeleteRequest extends BaseFormRequest
{

    public function rules()
    {
        $user_id = (int)getLoginHome('id');
        return [
            'id' => ['required', 'integer', Rule::exists(UserFollowIndustryModel::TABLE_NAME)
                ->where('user_id', $user_id)
                ->whereNot('type',  USER_FOLLOW_INDUSTRY_TYPE['main'])
                ->whereNull('deleted_at')],
        ];
    }
}