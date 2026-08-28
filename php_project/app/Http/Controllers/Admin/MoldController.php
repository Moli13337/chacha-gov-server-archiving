<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/18
 * Time: 10:32
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Repositories\Policy\MoldRepository;

class MoldController extends Controller
{

    protected $repository;

    public function __construct(MoldRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $data = $this->repository->allList(['id', 'name']);

        return codeRender(Code::OK, $data);
    }
}