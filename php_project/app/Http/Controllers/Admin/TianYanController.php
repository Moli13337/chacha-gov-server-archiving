<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/21
 * Time: 13:56
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\TianYanService;
use App\Http\Requests\TianYan\DetailRequest;
use App\Http\Requests\TianYan\ListRequest;

class TianYanController extends Controller
{

    protected $tyService;

    public function __construct(TianYanService $tyService)
    {
        $this->tyService = $tyService;
    }

    public function list(ListRequest $request)
    {
        $data = $this->tyService->getOrgList(['org_name' => $request->input('name')]);
        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->tyService->getOrgDetail(['org_name' => $request->input('name')]);
        return codeRender(Code::OK, $data);
    }
}