<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 10:01
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Requests\Industry\SaveRequest;
use App\Repositories\IndustryRepository;
use Illuminate\Http\Request;

class IndustryController extends Controller
{

    protected $repository;

    public function __construct(IndustryRepository $repository)
    {
        $this->repository = $repository;
    }

//    public function store(SaveRequest $request)
//    {
//        $res = $this->repository->storeRepository($this->filter($request));
//
//        return codeRender(Code::OK, $res);
//    }

    public function index(Request $request)
    {
        $data = $this->repository->getTree(['id', 'category', 'b_type', 'm_type', 's_type', 'type_name']);

        return codeRender(Code::OK, $data);
    }

    public function v2(Request $request)
    {

        $data = app(IndustryService::class)->v2();

        return codeRender(Code::OK, $data);
    }

}