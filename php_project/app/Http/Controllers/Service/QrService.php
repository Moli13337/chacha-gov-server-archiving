<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/4
 * Time: 23:04
 */

namespace App\Http\Controllers\Service;


use App\Common\Code;
use App\Exceptions\CodeException;
use App\Exceptions\QrCodeException;
use Zxing\QrReader;

class QrService
{
    public function __construct()
    {

    }

    /**
     * FUNCTION_NAME : reader
     * author : jp
     * 解析二维码
     * @param $path 地址/链接
     * @return mixed
     * @throws CodeException
     */
    public function reader($path)
    {
        $qr = new QrReader($path);
        $text = $qr->text();
        if (empty($text)) {
            throw new QrCodeException(Code::QR_CODE_READER_ERROR);
        }
        return $text;
    }
}