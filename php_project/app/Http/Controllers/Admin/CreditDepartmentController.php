<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/21
 * Time: 17:34
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Repositories\Enterprise\CreditDepartmentRepository;
use Illuminate\Http\Request;

class CreditDepartmentController extends Controller
{

    protected $creditDepartmentRepository;

    public function __construct(CreditDepartmentRepository $creditDepartmentRepository)
    {
        $this->creditDepartmentRepository = $creditDepartmentRepository;
    }

    public function list(Request $request)
    {
        $data = $this->creditDepartmentRepository->getList(['id','name']);

        return codeRender(Code::OK, $data);
    }
}