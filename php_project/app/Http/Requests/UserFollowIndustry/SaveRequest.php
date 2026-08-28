<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 18:54
 */

namespace App\Http\Requests\UserFollowIndustry;


use App\Http\Requests\BaseFormRequest;

class SaveRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'main' => ['required', 'array'],
            'main.id' => ['nullable', 'integer'],
            'main.first_industry_id' => ['required', 'integer'],
            'main.second_industry_id' => ['nullable', 'integer'],
            'main.third_industry_id' => ['nullable', 'integer'],
            'main.fourth_industry_id' => ['nullable', 'integer'],

            'vice' => ['nullable', 'array'],
            'vice.id' => ['nullable', 'integer'],
            'vice.first_industry_id' => ['nullable', 'integer'],
            'vice.second_industry_id' => ['nullable', 'integer'],
            'vice.third_industry_id' => ['nullable', 'integer'],
            'vice.fourth_industry_id' => ['nullable', 'integer'],

            'follow' => ['nullable', 'array', 'max:20'],
            'follow.*.id' => ['nullable', 'integer'],
            'follow.*.first_industry_id' => ['required', 'integer'],
            'follow.*.second_industry_id' => ['nullable', 'integer'],
            'follow.*.third_industry_id' => ['nullable', 'integer'],
            'follow.*.fourth_industry_id' => ['nullable', 'integer'],

        ];
    }

    public function attributes()
    {
        return trans('validation.custom.follow_industry');
    }

    public function messages()
    {
        return [
            'follow.max' => trans('validation.custom.follow_industry.follow.max')
        ];
    }
}