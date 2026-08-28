<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/12
 * Time: 15:40
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginLogs\ListRequest;
use App\Repositories\LoginLogsRepository;

class LoginLogsController extends Controller
{

    protected $repository;

    public function __construct(LoginLogsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(ListRequest $request)
    {
        $param = $this->filter($request);
        $param['source_id'] = $request->input('user_id');
        $param['source_type'] = LOGIN_LOG_TYPE['user'];
        $param['order_by'] = ['id' => 'DESC'];
        $data = app(LoginLogsRepository::class)->list($param);
        return codeRender(Code::OK, $data);
    }
}