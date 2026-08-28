<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 17:28
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class Captcha implements Rule
{
    protected $params;

    public function __construct($left )
    {
        $this->params = $left;
    }

    public function passes($attribute, $value)
    {
        if (!Cache::has(REDIS_CAPTCHA.$this->params)) {
            return false;
        }
        $key = Cache::get(REDIS_CAPTCHA.$this->params, '');
        if (empty($key)) {
            return false;
        }
        $res = app('captcha')->check_api($value,$key);

        if ($res) {
            Cache::forget(REDIS_CAPTCHA.$this->params);
        }
        return $res;
    }

    public function message()
    {
        return trans('validation.captcha');
    }}