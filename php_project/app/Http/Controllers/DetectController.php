<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/19
 * Time: 11:04
 */

namespace App\Http\Controllers;


use App\Common\Code;
use Illuminate\Http\Request;

class DetectController extends Controller
{

    public function index(Request $request)
    {
        return codeRender(Code::OK, 'OK');
    }
}