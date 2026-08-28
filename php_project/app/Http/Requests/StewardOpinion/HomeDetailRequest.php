<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 10:02
 */

namespace App\Http\Requests\StewardOpinion;


use App\Http\Requests\BaseFormRequest;
use App\Models\Steward\StewardOpinionModel;
use Illuminate\Validation\Rule;

class HomeDetailRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'string', Rule::exists(StewardOpinionModel::TABLE_NAME, 'enc_id')->whereNull('deleted_at')],
        ];
    }
}