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
use App\Rules\Captcha;
use Illuminate\Validation\Rule;

class HomeSubmitRequest extends BaseFormRequest
{

    public function rules()
    {
        return [
            'id' => ['required', 'string', Rule::exists(StewardOpinionModel::TABLE_NAME,'enc_id')
                ->where('publish_status', PUBLISH_STATUS['yes'])->whereNull('deleted_at')],
            'content' => ['required', 'string', 'max:500'],
            'file' => ['nullable', 'array'],
            'file.*.name' => ['required', 'string'],
            'file.*.save_url' => ['required', 'string'],
            'key' => ['required', 'string'],
            'captcha' => ['required', new Captcha($this->input('key'))]
        ];
    }
}