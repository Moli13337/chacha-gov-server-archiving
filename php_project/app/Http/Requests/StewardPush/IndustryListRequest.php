<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 17:01
 */

namespace App\Http\Requests\StewardPush;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class IndustryListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            "keyword" => ['nullable', 'string'],
            "first_industry_id" => ['required', 'integer'],
            "second_industry_id" => ['nullable', 'integer'],
            "third_industry_id" => ['nullable', 'integer'],
            "fourth_industry_id" => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return trans('column.steward_publish');
    }
}