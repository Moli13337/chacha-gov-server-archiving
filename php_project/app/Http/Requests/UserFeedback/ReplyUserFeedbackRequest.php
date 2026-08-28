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

class ReplyUserFeedbackRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'source_id' => ['required', 'int'],
            'title' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string','max:500'],
        ];
    }
}