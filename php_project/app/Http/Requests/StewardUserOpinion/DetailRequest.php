<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 10:02
 */

namespace App\Http\Requests\StewardUserOpinion;


use App\Http\Requests\BaseFormRequest;
use App\Models\Steward\StewardUserOpinionModel;
use Illuminate\Validation\Rule;

class DetailRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(StewardUserOpinionModel::TABLE_NAME)],
        ];
    }
}