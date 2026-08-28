<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 10:02
 */

namespace App\Http\Requests\StewardUserOpinion;


use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'keyword' => ['nullable', 'string'],
            'steward_opinion_id' => ['required', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}