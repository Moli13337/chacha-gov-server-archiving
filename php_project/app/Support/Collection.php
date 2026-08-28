<?php

namespace App\Support;

use App\Common\Code;
use App\Exceptions\CodeException;

class Collection
{

    public static function obj2array($obj)
    {
        if (is_object($obj) === true) {
            if (method_exists($obj, "toArray")) {
                return $obj->toArray();
            } else {
                throw new CodeException(Code::SERVER_ERROR,['error_message'=>trans('errors.COLLECT_DATA_NOT_SUPPORT_METHOD')]);
            }
        } else {
            return $obj;
        }
    }

    public static function filter($keys, $data, $sign_name = 'keep')
    {
        if (is_array($keys) == false) {
            $keys = explode(",", $keys);
        }
        $sign = $sign_name == "keep" ? false : true;
        foreach ($data as $index => $index_data) {
            if (in_array($index, $keys) == $sign) {
                unset($data[$index]);
            }
        }
        return $data;
    }

    //*
    public static function filterMulti($keys, $data, $sign_name = 'keep')
    {
        $data = self::obj2array($data);
        foreach ($data as $index => $single_data) {
            $data[$index] = self::filter($keys, $single_data, $sign_name);
        }
        return $data;
    }

    public static function getValueWithKey($array, $key, $filed, $prefix = "")
    {
        $new_array = array();
        if (is_array($array) == true) {
            foreach ($array as $index => $single_value) {
                if (isset($single_value[$key]) && isset($single_value[$filed])) {
                    $new_array[$prefix . $single_value[$key]] = $single_value[$filed];
                }
            }
        }
        return $new_array;
    }
   
    
}

