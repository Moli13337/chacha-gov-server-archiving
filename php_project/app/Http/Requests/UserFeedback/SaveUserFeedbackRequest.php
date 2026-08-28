<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 9:43
 */

namespace App\Http\Requests\UserFeedback;


use App\Http\Requests\BaseFormRequest;
use App\Rules\Captcha;
use Illuminate\Validation\Rule;

class SaveUserFeedbackRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:20'],
            'content' => ['required', 'string', 'max:500'],
            'type' => ['required', 'integer', Rule::in(array_values(FEEDBACK_TYPE))],
            'key' => ['required', 'string'],
            'captcha' => ['required', new Captcha($this->input('key'))]
        ];
    }
}