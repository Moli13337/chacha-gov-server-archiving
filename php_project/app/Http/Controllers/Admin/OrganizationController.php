<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/4
 * Time: 15:29
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\QiChaChaService;
use App\Http\Requests\TianYan\DetailRequest;
use App\Http\Requests\TianYan\ListRequest;

class OrganizationController extends Controller
{

    protected $qiChaChaService;

    public function __construct(QiChaChaService $qiChaChaService)
    {
        $this->qiChaChaService = $qiChaChaService;
    }

    public function list(ListRequest $request)
    {
        $data = $this->qiChaChaService->getOrgList($request->input('name'));
        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->qiChaChaService->getOrgDetail($request->input('name'));
//        $data = $this->qiChaChaService->getOrgDetailFull($request->input('name'));
        return codeRender(Code::OK, $data);
    }}