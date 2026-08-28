<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 14:05
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Models\GovAgenModel;
use App\Repositories\Policy\GovAgenRepository;
use Illuminate\Http\Request;

class GovAgenController extends Controller
{

    protected $repository;

    public function __construct(GovAgenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $data = $this->repository->getTree(['id','gov_agen_name','parent_id']);

        return codeRender(Code::OK, $data);
    }
}